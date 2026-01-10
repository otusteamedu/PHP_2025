# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

Быстрый старт

1. Запустить окружение: `docker-compose up -d`

2. Применить миграции:

   - `docker-compose exec app php mysite.local/Migrations/001_create_movie_table.php`
   - `docker-compose exec app php mysite.local/Migrations/002_seed_movie_table.php`

3. Отправлять запросы на `http://localhost/movies`:

- `GET /movies` — список фильмов
- `GET /movies/{id}` — фильм по id
- `POST /movies` — создать (title, duration, description?)
- `DELETE /movies/{id}` — удалить
