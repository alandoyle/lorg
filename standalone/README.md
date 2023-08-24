## Standalone Instructions

Tested on Debian 11/Ubuntu 22.04 but should run on any recent version of Linux with nginx and PHP 7 or higher.

> sudo apt install php php-fpm php-dom php-curl nginx git -y
> sudo systemctl enable nginx

A sample Nginx site configuration file can be found in *standalone/lorg.nginx.conf*.

> git clone https://github.com/alandoyle/lorg /var/www/lorg

### Install the default files.

> sudo cp /var/www/lorg/docker/config.php /var/www/lorg/config
> sudo cp /var/www/lorg/docker/custom.css /var/www/lorg/custom

### Configure nginx

> sudo rm /etc/nginx/sites-enabled/default
> sudo cp /var/www/lorg/standalone/lorg.nginx.conf /etc/nginx/sites-available/lorg
> sudo ln -s /etc/nginx/sites-available/lorg /etc/nginx/sites-enabled/lorg
> sudo nginx -t
> sudo systemctl restart nginx

**lorg** is now running on port 80. Ideally it should be run over HTTPS either directly from this instance of *nginx* or via Nginx Proxy Mnager/HAProxy/Traefic/etc. This is beyond the scope of this instruction set and well documented elsewhere on the web.
