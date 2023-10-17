#!/bin/sh -e
#
# lorg - startup.sh
#


DST_DIR=/var/www/lorg
SRC_REPO=https://github.com/alandoyle/lorg

# Create the 'app' user
if ! id app >/dev/null 2>&1; then
	addgroup --gid $OWNER_GID app
	useradd -d /var/www/lorg -g app -u $OWNER_UID app
   usermod -aG www-data,app apps
fi

if [ ! -d $DST_DIR/.git ]; then
	mkdir -p $DST_DIR
	chown $OWNER_UID:$OWNER_GID $DST_DIR

	echo cloning lorg source from $SRC_REPO to $DST_DIR...
	sudo -u app git clone $SRC_REPO $DST_DIR || echo error: failed to clone master repository.
else
	echo updating lorg source in $DST_DIR from $SRC_REPO...

	chown -R $OWNER_UID:$OWNER_GID $DST_DIR
	cd $DST_DIR && \
		sudo -u app git config core.filemode false && \
		sudo -u app git config pull.rebase false && \
		sudo -u app git pull origin master || echo error: unable to update master repository.
fi

update-ca-certificates || true

if [ ! -e $DST_DIR/public/index.php ]; then
	echo "error: lorg index.php missing (git clone failed?) [$DST_DIR], unable to continue."
	ls -l $DST_DIR
	exit 1
fi

if [ ! -f ${DST_DIR}/config/config.php ] ; then
    cp ${DST_DIR}/docker/config.php ${DST_DIR}/config/config.php
fi
chmod 644 $DST_DIR/config/config.php

# Configure PHP
echo "Setting PHP memory_limit to ${PHP_WORKER_MEMORY_LIMIT}"
sed -i.bak "s/^\(memory_limit\) = \(.*\)/\1 = ${PHP_WORKER_MEMORY_LIMIT}/" \
	/etc/php/8.1/fpm/php.ini

echo "Setting PHP pm.max_children to ${PHP_WORKER_MAX_CHILDREN}"
sed -i.bak "s/^\(pm.max_children\) = \(.*\)/\1 = ${PHP_WORKER_MAX_CHILDREN}/" \
	/etc/php/8.1/fpm/pool.d/www.conf

rm -f /tmp/error.log && mkfifo /tmp/error.log && chown app:app /tmp/error.log

(tail -q -f /tmp/error.log >> /proc/1/fd/2) &

if [ -d ${DST_DIR}/template.original ] ; then
    cp -Rf ${DST_DIR}/template.original/* ${DST_DIR}/template
fi

touch $DST_DIR/.app_is_ready

# Run it all :)
echo "Starting daemons..."
/usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
