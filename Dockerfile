#Dockerfile.
FROM ubuntu:jammy

ARG DEBIAN_FRONTEND="noninteractive"

ENV SCRIPT_ROOT=/var/www/lorg

VOLUME ${SCRIPT_ROOT}/config
VOLUME ${SCRIPT_ROOT}/template

# Install software
RUN apt-get -qq update -y && apt-get -qq upgrade -y && apt-get -qq install git sudo -y
RUN apt-get -qq install nginx-core php php-fpm php-common php-curl php-dom tzdata supervisor -y
RUN rm -rf /tmp/* /var/lib/apt/lists/* /var/tmp/*
RUN sed -i -e 's/;\(clear_env\) = .*/\1 = no/i' \
		-e 's/^\(user\|group\) = .*/\1 = app/i' \
		-e 's/;\(php_admin_value\[error_log\]\) = .*/\1 = \/tmp\/error.log/' \
		-e 's/;\(php_admin_flag\[log_errors\]\) = .*/\1 = on/' \
        /etc/php/8.1/fpm/pool.d/www.conf
RUN mkdir -p /etc/nginx/global /var/www

# Configure Image
COPY docker/restrictions.conf /etc/nginx/global
COPY standalone/lorg.nginx.conf /etc/nginx/sites-enabled/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log \
	&& ln -sf /dev/stderr /var/log/php8.1-fpm.log

# HTTP
EXPOSE 80/tcp

ENV OWNER_UID=1000
ENV OWNER_GID=1000

ENV PHP_WORKER_MAX_CHILDREN=5
ENV PHP_WORKER_MEMORY_LIMIT=256M

COPY docker/startup.sh /startup.sh
RUN chmod 755 /startup.sh

CMD ["/startup.sh"]

LABEL maintainer="me@alandoyle.com"