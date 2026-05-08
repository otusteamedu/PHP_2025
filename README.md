# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Event storage CLI

Запуск контейнеров:

```bash
docker-compose up -d
```

Остановка контейнеров:

```bash
docker-compose down
```

Redis Insight:

```text
http://localhost:5540
```

Подключение к Redis в Redis Insight:

```text
redis://user:password@redis:6379
```

Очистить все события:

```bash
docker-compose exec app php /data/mysite.local/index.php clear
```

Добавить событие:

```powershell
docker-compose exec app php /data/mysite.local/index.php add --priority=1000 --conditions='{\"param1\":1}' --event='{\"name\":\"event_1\"}'
```

Добавить событие с несколькими условиями:

```powershell
docker-compose exec app php /data/mysite.local/index.php add --priority=3000 --conditions='{\"param1\":1,\"param2\":2}' --event='{\"name\":\"event_3\"}'
```

Найти наиболее подходящее событие:

```powershell
docker-compose exec app php /data/mysite.local/index.php find --params='{\"param1\":1,\"param2\":2}'
```

Посмотреть справку:

```bash
docker-compose exec app php /data/mysite.local/index.php help
```

Пример Redis-ключей:

```text
events:conditions:param1=1
events:conditions:param1=1|param2=2
events:condition_keys
```

Логика хранения в Redis:

- условия события сортируются по названию параметра;
- Redis-ключ строится из полного набора условий события;
- формат ключа: `events:conditions:param1=1|param2=2`;
- события хранятся в Redis sorted set;
- score в sorted set равен priority события;
- `events:condition_keys` хранит все созданные ключи условий для последующей очистки.

Логика поиска:

- входящие параметры пользователя разбиваются на все возможные подмножества;
- для каждого подмножества строится Redis-ключ в том же формате;
- приложение проверяет подходящие sorted set и выбирает событие с максимальным priority;
- например, параметры `{"param1":1,"param2":2}` дают такие ключи:

```text
events:conditions:param1=1
events:conditions:param2=2
events:conditions:param1=1|param2=2
```
