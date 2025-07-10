# Домашнее задание №10 - Менеджер событий

## Структура проекта

```
event-manager/
├── app/
│   ├── Storage/
│   │   ├── StorageInterface.php
│   │   ├── RedisStorage.php
│   │   ├── MongoStorage.php
│   │   └── ElasticsearchStorage.php
│   ├── EventManager.php
│   └── ConsoleApp.php
├── vendor/
└── bin/
    └── console
```

## Запуск и работа

1. Устанавливаем зависимости

```shell
composer install
```

2. Запускаем контейнеры

```shell
docker-compose -f ./docker/docker-compose.yaml up -d
```

3. Добавляем тестовые данные

- Redis

```shell
php bin/console add '{"priority":1000,"conditions":{"param1":1},"event":"::event::"}'
php bin/console add '{"priority":2000,"conditions":{"param1":2,"param2":2},"event":"::event::"}'
php bin/console add '{"priority":3000,"conditions":{"param1":1,"param2":2},"event":"::event::"}'
```

- MongoDB

```shell
php bin/console mongo add '{"priority":1000,"conditions":{"param1":1},"event":"::event::"}'
php bin/console mongo add '{"priority":2000,"conditions":{"param1":2,"param2":2},"event":"::event::"}'
php bin/console mongo add '{"priority":3000,"conditions":{"param1":1,"param2":2},"event":"::event::"}'
```

- Elasticsearch

```shell
php bin/console elastic add '{"priority":1000,"conditions":{"param1":1},"event":"::event::"}'
php bin/console elastic add '{"priority":2000,"conditions":{"param1":2,"param2":2},"event":"::event::"}'
php bin/console elastic add '{"priority":3000,"conditions":{"param1":1,"param2":2},"event":"::event::"}'
```

### Доступные команды

#### Добавить событие

```bash
php bin/console <storage> add '<event>'
```

#### Очистить все события

```bash
php bin/console <storage> clear
```

#### Найти подходящее событие

```bash
php bin/console <storage> find '<message>'
```

> Storage по умолчанию - Redis