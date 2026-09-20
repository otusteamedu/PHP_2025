# ДЗ-11 Redis

Хранилище событий с условиями и приоритетами. По запросу возвращается событие,
для которого выполнены все условия, с наибольшим `priority`.

## Запуск

```bash
cd redis-events
docker compose up -d
composer install
composer test
```

Redis слушает `127.0.0.1:6381`.

## Пример

```bash
php bin/events clear
php bin/events add 1000 '{"param1":1}' '{"name":"first"}'
php bin/events add 2000 '{"param1":2,"param2":2}' '{"name":"second"}'
php bin/events add 3000 '{"param1":1,"param2":2}' '{"name":"third"}'
php bin/events find '{"param1":1,"param2":2}'
```

Последняя команда вернёт `third` с `priority: 3000`. Для быстрого запуска
этого примера есть команда `php bin/events demo`.

## Хранение

- `events:data:{id}` — JSON события;
- `events:conditions:{условия}` — sorted set, где score равен `priority`;
- `events:ids` и `events:condition-keys` — служебные ключи для `clear()`.

Код зависит от `EventStorageInterface`, поэтому Redis можно заменить другой
реализацией. В проекте есть `InMemoryEventStorage` для проверки этого
контракта.
