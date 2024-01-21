## Standalone Instructions

Tested on Debian 11/Ubuntu 22.04 but should run on any recent version of Linux with nginx and PHP 7 or higher.

```bash
sudo apt install php php-fpm php-dom php-curl nginx git -y
sudo systemctl enable nginx
```

A sample Nginx site configuration file can be found in *standalone/lorg.nginx.conf*.

```bash
sudo git clone https://github.com/alandoyle/lorg /usr/share/lorg
```

### Install the default configuration.

```bash
sudo mkdir -p  /etc/lorg/config /etc/lorg/template
sudo cp /usr/share/lorg/docker/config.php /etc/lorg/config
sudo cp -R /usr/share/lorg/template/* /etc/lorg/template
```

### Add extra instances (optional)

```bash
sudo cp /usr/share/lorg/docker/instances.json.example /etc/lorg/config/instances.json
```

NOTE: The local instance is automatically added to the instance list unless disabled in `config.php`

### Configure nginx

```bash
sudo rm /etc/nginx/sites-enabled/default
sudo cp /usr/share/lorg/standalone/lorg.nginx.conf /etc/nginx/sites-available/lorg
sudo ln -s /etc/nginx/sites-available/lorg /etc/nginx/sites-enabled/lorg
sudo nginx -t
sudo systemctl restart nginx
```

**lorg** is now running on port 80. Ideally it should be run over HTTPS either directly from this instance of *nginx* or via Nginx Proxy Manager/HAProxy/Traefik/etc. This is beyond the scope of this instruction set and is well documented elsewhere on the web.
