# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Описание

### Архитектура

Проект представляет собой трёхуровневую масштабируемую систему с балансировкой нагрузки:

1. **Frontend Nginx** (`php_course_nginx`) — входной балансировщик на порту 80
   - Распределяет HTTP-запросы между тремя backend nginx (стратегия: random)
2. **Backend Nginx** (`backend_nginx_1/2/3`) — три экземпляра application-серверов
   - Каждый балансирует запросы между тремя PHP-FPM контейнерами (стратегия: random)
   - Обрабатывают статику и проксируют PHP-запросы в FPM-пул
3. **PHP-FPM** (`php_fpm_1/2/3`) — три контейнера с PHP 8.2

   - Исполняют приложение `code/index.php`
   - Хранят сессии в Redis Cluster через расширение phpredis

4. **Redis Cluster** (`redis-1/2/3`) — три мастер-узла без реплик
   - Распределённое хранилище сессий (16384 хэш-слота)
   - Автоматическая инициализация через сервис `redis-init`
   - Персистентность: AOF (appendonly) + RDB snapshots

### Функциональность приложения

Приложение (`code/index.php`) обрабатывает `POST` параметр `string`:

- Проверка наличия параметра;
- Проверка, что после `trim` значение не пустое;
- Если в строке присутствуют скобки `(` и `)`, выполняется проверка валидности:
  баланс и порядок скобок должны быть корректными.

Все 404 в nginx направляются на `index.php`. Прямой доступ к `*.php` запрещён.

## Условия ответов

- 200 OK:
  - Параметр `string` присутствует и после `trim` не пустой;
  - Либо скобок нет вовсе;
  - Либо скобки присутствуют и валидны (количество совпадает, порядок корректный).
  - Также при любом `GET` запросе возвращается 200 OK, даже если путь не существует (404 в nginx перенаправляется на `index.php`, который отвечает 200).
- 400 Bad Request:
  - Отсутствует обязательный параметр `string`;
  - В параметре `string` отсутствует значение (после `trim` пусто);
  - Невалидное значение: скобки присутствуют, но количество открытых/закрытых не совпадает или порядок неверный (например, `)(`).

## Запуск

```powershell
docker-compose build
docker-compose up -d
```

При первом запуске сервис `redis-init` автоматически инициализирует Redis Cluster (распределит 16384 хэш-слота между тремя мастерами).

Приложение доступно на `http://localhost/` или по любому доменному имени, добавленному в файл `hosts` вашей ОС и указывающему на `127.0.0.1`.

### Проверка состояния кластера

```powershell
# Проверить статус Redis Cluster
docker exec redis-1 redis-cli cluster info

# Посмотреть распределение слотов
docker exec redis-1 redis-cli cluster nodes

# Проверить работу сессий (в ответе должен быть PHPSESSID)
curl -i http://localhost/
```

## Примеры запросов

```powershell
# 400 — отсутствует обязательный параметр string
curl -i -X POST http://localhost/

# 400 — пустое значение после trim
curl -i -X POST -H "Content-Type: application/x-www-form-urlencoded" -d "string=   " http://localhost/

# 200 — текст без скобок
curl -i -X POST -H "Content-Type: application/x-www-form-urlencoded" -d "string=какой-то текст" http://localhost/

# 400 — неверный порядок скобок
curl -i -X POST -H "Content-Type: application/x-www-form-urlencoded" -d "string=)(" http://localhost/

# 400 — несовпадающее количество
curl -i -X POST -H "Content-Type: application/x-www-form-urlencoded" -d "string=(()" http://localhost/

# 200 — валидные скобки
curl -i -X POST -H "Content-Type: application/x-www-form-urlencoded" -d "string=()()(()())" http://localhost/

# 200 — GET запрос показывает информацию о сессии и контейнере
curl -i http://localhost/

# 200 → 404 обрабатывается index.php
curl -i http://localhost/not-found
```

### Проверка балансировки

Несколько запросов покажут разные контейнеры благодаря балансировке:

```powershell
# Выполните несколько раз и проверьте поле "Контейнер:" в ответе
curl http://localhost/
curl http://localhost/
curl http://localhost/
```

Сессии сохраняются в Redis Cluster, поэтому параметр `visits` должен увеличиваться независимо от того, какой контейнер обработал запрос.

## Технические детали

### Конфигурация сессий PHP

В `fpm/php.ini` настроено хранение сессий через Redis Cluster:

```ini
session.save_handler = rediscluster
session.save_path = "seed[]=redis-1:6379&seed[]=redis-2:6379&seed[]=redis-3:6379&timeout=3&read_timeout=3&failover=distribute&persistent=0&prefix=PHPREDIS_SESSION:"
```

- **timeout=3, read_timeout=3** — таймауты подключения/чтения (достаточны для DNS-резолвинга)
- **failover=distribute** — при недоступности узла запросы переключаются на другие узлы
- **persistent=0** — отключены постоянные соединения для избежания проблем с устаревшими DNS-записями
- **prefix=PHPREDIS_SESSION:** — префикс ключей сессий в Redis

### Защита от временных сбоев Redis

В `code/index.php` реализована функция `startSessionSafe()` с retry-логикой:

- 3 попытки запуска сессии с задержкой 100мс между ними
- При полном отказе Redis возвращается HTTP 503 вместо PHP Fatal Error
- Предотвращает каскадные ошибки "headers already sent"

### Структура контейнеров

```
Клиент → php_course_nginx:80
           ↓ (random)
         backend_nginx_1/2/3
           ↓ (random)
         php_fpm_1/2/3
           ↓
         redis-1/2/3 (cluster)
```

## Troubleshooting

### Redis Cluster не инициализируется

```powershell
# Проверьте логи инициализации
docker logs redis-init

# Вручную пересоздайте кластер
docker exec -it redis-1 redis-cli --cluster create redis-1:6379 redis-2:6379 redis-3:6379 --cluster-replicas 0
```

### Ошибка "Couldn't map cluster keyspace"

Кластер не инициализирован. Проверьте статус:

```powershell
docker exec redis-1 redis-cli cluster info
```

Если `cluster_state:fail`, пересоздайте командой выше.

### Сессии не работают / 503 Service Unavailable

```powershell
# Проверьте доступность всех узлов Redis
docker exec redis-1 redis-cli ping
docker exec redis-2 redis-cli ping
docker exec redis-3 redis-cli ping

# Проверьте, что PHP-FPM может резолвить имена
docker exec php_fpm_1 getent hosts redis-1
docker exec php_fpm_1 getent hosts redis-2
docker exec php_fpm_1 getent hosts redis-3
```

### Контейнеры не меняются при запросах

Это нормально при малом количестве запросов — стратегия `random` не гарантирует равномерное распределение на коротких интервалах. Выполните 10-20 запросов подряд, чтобы увидеть разные контейнеры.
