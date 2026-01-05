# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

Сервис событий (правила + матчинг) с двумя хранилищами: Redis и Memcached.

## Запуск

- Использовать существующий `.env` или скопировать свой пример: `cp .env .env.local` и при необходимости поменять переменные.
- По умолчанию включен Redis.
- Собрать и поднять контейнеры: `docker-compose up -d`.

## Переключение хранилища

- Управляется переменной `EVENTS_STORAGE` в `.env`.
  - `redis` — хранилище в Redis (использует `REDIS_HOST`, `REDIS_PORT`).
  - `memcached` — хранилище в Memcached (использует `MEMCACHED_HOST`, `MEMCACHED_PORT`).
- После изменения переменной нужно перезапустить сервис: `docker-compose up -d`.

## Как добавить новое хранилище

1. Реализовать интерфейс `App\\Storage\\EventsStorageInterface` в новом классе (см. [RedisEventsStorage](code/src/Storage/RedisEventsStorage.php) или [MemcachedEventsStorage](code/src/Storage/MemcachedEventsStorage.php)).
2. Повторно использовать трейт `App\\Storage\\EventsMatcherTrait` для фильтрации и сортировки совпадений.
3. Подключить класс в `App\\App::resolveStorage()` (и при необходимости добавить переменные окружения).
4. Перезапустить контейнер `app`: `docker-compose up -d`.

## Кратко по API

- POST `/events` — добавить событие с полями `priority`, `conditions`, `event`.
  Пример тела запроса (JSON):

  ```json
  {
    "priority": 3000,
    "conditions": {
      "param1": 1,
      "param2": 2
    },
    "event": {
      "payload": "::event::"
    }
  }
  ```

- DELETE `/events` — очистить все события.
- POST `/events/match` — найти подходящие события по `params`.
  Пример тела запроса (JSON):
  ```json
  {
    "params": {
      "param1": 1,
      "param2": 2
    }
  }
  ```
