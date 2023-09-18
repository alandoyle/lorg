# lorg

[(Pronounced Lur-Ugh - An Irish word meaning "Track, trace; seek, search for." )](https://www.teanglann.ie/en/fgb/lorg)

A framework and javascript free privacy respecting Google Text/Image Proxy originally based on a cut-down version of LibreX but now evolved into simple, lightweight API driven Meta Search Engine.

![lorg screenshot](https://raw.githubusercontent.com/alandoyle/lorg/main/screenshot/screenshot1.png)

## Docker Setup (Recommended)

Available on [DockerHub](https://hub.docker.com/r/alandoyle/lorg)
```bash
docker pull alandoyle/lorg
```

### Usage

```bash
docker run --name=lorg \
  -d --init \
  -v <MY_CONFIG_PATH>:/var/www/lorg/config \
  -v <MY_TEMPLATE_PATH>:/var/www/lorg/template \
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
     - ./lorg/config:/var/www/lorg/config
     - ./lorg/template:/var/www/lorg/template
```

### Ports

| Port     | Description           |
|----------|-----------------------|
| `80/tcp` | HTTP                  |

### Volumes

| Path                     | Description                       |
|--------------------------|-----------------------------------|
| `/var/www/lorg/config`   | path for lorg configuration files |
| `/var/www/lorg/template` | path for lorg template files      |

## Standalone Setup

See [(Standalone Instructions)](https://github.com/alandoyle/lorg/blob/main/standalone/README.md) for more information.
