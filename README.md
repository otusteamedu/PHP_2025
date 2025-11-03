# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

PHP
---

```shell
docker build . --tag=otus-php-2025/nginx:latest --file=docker/build/nginx/Dockerfile --target=production
```

nginx
-----

```shell
docker build . --tag=otus-php-2025/php:latest --file=docker/build/php/Dockerfile --target=production
```

```yaml
    nginx:
        image: otus-php-2025/nginx:latest
        volumes:
            - php-fpm-sock:/var/www/run/:rw
        depends_on:
            - php
        networks:
            private:

    php:
        image: otus-php-2025/php:latest
        volumes:
            - php-fpm-sock:/var/www/run/:rw
        depends_on:
            - memcached
            - redis
            - postgres
        networks:
            private:
```
