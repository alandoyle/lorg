#!/bin/sh -e
#
# lorg - startup.sh
#
DST_DIR=/etc/lorg
GIT_DIR=/usr/share/lorg
SRC_REPO=https://github.com/alandoyle/lorg

# Get latest version from GIT
if [ ! -d $GIT_DIR/.git ]; then
	[ ! -d $DST_DIR ] && mkdir -p $DST_DIR
	chown -R $OWNER_UID:$OWNER_GID $DST_DIR

	echo cloning lorg source from $SRC_REPO to $GIT_DIR...
	git clone $SRC_REPO $GIT_DIR || echo error: failed to clone repository.
else
	echo updating lorg source in $GIT_DIR from $SRC_REPO...

	cd $GIT_DIR && \
		git config core.filemode false && \
		git config pull.rebase false && \
		git pull || echo error: unable to update repository.
fi

# Update certificates
update-ca-certificates || true

# Check if GIT worked.
if [ ! -e $GIT_DIR/public/index.php ]; then
	echo "error: lorg index.php missing (git clone failed?) [$DST_DIR], unable to continue."
	ls -l $GIT_DIR
	exit 1
fi

# Check if we have a config file
if [ ! -f ${DST_DIR}/config/config.php ] ; then
	[ ! -d ${DST_DIR}/config ] && mkdir -p ${DST_DIR}/config
    cp ${GIT_DIR}/docker/config.php ${DST_DIR}/config/config.php
fi

# Check if we have templates (at least the latest default 'lorg' template)
[ ! -d ${DST_DIR}/template ] && mkdir -p ${DST_DIR}/template
[ -d ${DST_DIR}/template/lorg ] && rm -rf ${DST_DIR}/template/lorg
cp -Rf ${GIT_DIR}/template/* ${DST_DIR}/template

# Set permissions
chmod -R a=r,a+X,u+w $DST_DIR

# Configure PHP
echo "Setting PHP memory_limit to ${PHP_WORKER_MEMORY_LIMIT}"
sed -i.bak "s/^\(memory_limit\) = \(.*\)/\1 = ${PHP_WORKER_MEMORY_LIMIT}/" \
	/etc/php/8.1/fpm/php.ini

echo "Setting PHP pm.max_children to ${PHP_WORKER_MAX_CHILDREN}"
sed -i.bak "s/^\(pm.max_children\) = \(.*\)/\1 = ${PHP_WORKER_MAX_CHILDREN}/" \
	/etc/php/8.1/fpm/pool.d/www.conf

# Create error log
rm -f /tmp/error.log && mkfifo /tmp/error.log

# Make sure the PHP FPM socket symlink exists
if [ ! -h /var/run/php-fpm.sock ] ; then
	ln -s /var/run/php/php8.1-fpm.pid /var/run/php/php-fpm.pid
fi

(tail -q -f /tmp/error.log >> /proc/1/fd/2) &

touch $DST_DIR/.app_is_ready

# Run it all :)
echo "Starting daemons..."
/usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
