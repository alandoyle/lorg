# lorg

(Pronounced Lur-Ugh - An Irish word meaning "Track, trace; seek, search for." )[https://www.teanglann.ie/en/fgb/lorg]

A framework and javascript free privacy respecting Google Proxy originally based on a cut-down version of LibreX but now evolved into lightweight, powerful API driven Google Meta Search Engine.

## Docker Setup

Available on [DockerHub](https://hub.docker.com/r/alandoyle/lorg)
```bash
docker pull alandoyle/lorg
```

### Usage

```bash
docker run --name=lorg \
  -d --init \
  -v <MY_CONFIG_PATH>:/var/www/config \
  -v <MY_CUSTOM_PATH>:/var/www/custom \
  -p 8000:80/tcp \
  alandoyle/lorg:latest
```

Docker compose example:

```yaml
version: "3"

services:
  lorg:
   image: alandoyle/lorg:latest
   container_name: lorg
   restart: unless-stopped
   init: true
   ports:
     - "8000:80/tcp"
   volumes:
     - ./lorg/config:/var/www/config
     - ./lorg/data:/var/www/custom
```

### Ports

| Port     | Description           |
|----------|-----------------------|
| `80/tcp` | HTTP                  |

### Volumes

| Path    | Description                           |
|---------|---------------------------------------|
| `/var/www/config` | path for lorg configuration files |
| `/var/www/custom` | path for lorg ovwerride files     |

## Standalone Setup

Tested on Debian 11/Ubuntu 22.04 but should run on any recent version of Linux with nginx and PHP 7 or higher.

> sudo apt install php php-fpm php-dom php-curl nginx git -y

A sample Nginx site configuration file can be found in *standalone/lorg.nginx.conf*.
