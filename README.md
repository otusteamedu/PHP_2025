### Описание выполненного домашнего задания №1

Представлены две конфигурации среды на Docker для PHP-приложения с использованием Nginx, PHP-FPM, Redis, Memcached и PostgreSQL.

- docker_tcp/ — подключение Nginx и PHP-FPM по TCP-порту.
- docker_unix/ — подключение Nginx и PHP-FPM через UNIX-сокет.

Для запуска перейдите в нужный каталог и выполните:
```bash
docker-compose build && docker-compose up -d
```