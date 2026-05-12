# PHP DataMapper + Identity Map

Консольное PHP-приложение для практики реализации паттернов работы с БД.

Приложение работает с таблицей `users`:

```text
id    SERIAL PRIMARY KEY
name  VARCHAR(255) NOT NULL
phone BIGINT NULL
```

Реализованы паттерны:

- `Singleton` - класс `App\Database\Connection` хранит единственное PDO-подключение.
- `DataMapper` - `App\Mapper\UserMapper` работает с таблицей `users` и преобразует строки БД в объекты `User`.
- `Identity Map` - `App\IdentityMap\UserIdentityMap` хранит объекты `User` в Memcached и помогает не создавать дубликаты объектов для одной строки БД.
- `Collection` - `App\Collection\UserCollection` возвращается из метода массового получения `findAll()`.

## Запуск контейнеров

```bash
docker-compose up -d --build
```

## Остановка контейнеров

```bash
docker-compose down
```

Если нужно полностью пересоздать данные PostgreSQL и заново выполнить init-скрипты, остановите контейнеры и очистите директорию `postgres/data`.

```bash
docker-compose down
```

Init-скрипт PostgreSQL из `postgres/init` выполняется только при первом создании данных контейнера.

Если данные PostgreSQL уже существовали до добавления init-скрипта и БД `database_patterns` не была создана, выполните:

```bash
docker-compose exec postgres psql -U postgres -d postgres -c "CREATE DATABASE database_patterns"
```

## Подготовка приложения

После запуска контейнеров выполните генерацию autoload:

```bash
docker-compose exec app composer dump-autoload --working-dir=/data/mysite.local
```

Затем создайте таблицу `users` и заполните ее тестовыми данными:

```bash
docker-compose exec app php /data/mysite.local/index.php init
```

## Команды приложения

Показать справку:

```bash
docker-compose exec app php /data/mysite.local/index.php help
```

Показать всех пользователей:

```bash
docker-compose exec app php /data/mysite.local/index.php list
```

Показать пользователя по id:

```bash
docker-compose exec app php /data/mysite.local/index.php get 1
```

Создать пользователя с телефоном:

```bash
docker-compose exec app php /data/mysite.local/index.php create "Ivan" 89990001122
```

Создать пользователя без телефона:

```bash
docker-compose exec app php /data/mysite.local/index.php create "Anna"
```

Обновить пользователя с телефоном:

```bash
docker-compose exec app php /data/mysite.local/index.php update 1 "Ivan Petrov" 79990002233
```

Обновить пользователя без телефона:

```bash
docker-compose exec app php /data/mysite.local/index.php update 1 "Ivan Petrov"
```

Удалить пользователя:

```bash
docker-compose exec app php /data/mysite.local/index.php delete 1
```
