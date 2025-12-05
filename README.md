# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Описание

- Веб-сервер `nginx` принимает все запросы на `http://localhost/` и проксирует динамику в `php-fpm`.
- Приложение (`code/index.php`) обрабатывает `POST` параметр `string`:
  - Проверка наличия параметра;
  - Проверка, что после `trim` значение не пустое;
  - Если в строке присутствуют скобки `(` и `)`, выполняется проверка валидности:
    баланс и порядок скобок должны быть корректными.
- Все 404 во `nginx` направляются на `index.php`. Прямой доступ к `*.php` запрещён.

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

Приложение доступно на `http://localhost/` или по любому доменному имени, добавленному в файл `hosts` вашей ОС и указывающему на `127.0.0.1`.

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

# 200 → обрабатывается index.php (внутренне), но для клиента придёт контент index.php
curl -i http://localhost/not-found
```
