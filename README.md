# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus


# Приложения для хранения событий

Приложение умеет:
- добавлять новое событие в систему хранения событий
- очищать все доступные события
- отвечать на запрос пользователя наиболее подходящим событием

Для хранения можно использовать Redis или MongoDB

## Добавление события
Endpoint: `POST /add`

Параметры:

`event` (string) - название события
`conditions` (string) - условия события в формате JSON объекта
`priority` (integer) - приоритет события (чем выше число, тем выше приоритет)


## Поиск события

Endpoint: `POST /find`
Параметры:
`conditions` (string) - условия для поиска в формате JSON объекта


## Удаление всех событий

Endpoint: `GET /delete-all`

Параметры:
Параметров нет



