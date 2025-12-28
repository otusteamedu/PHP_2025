# OTUS console application PHP

Поиск по книжному интернет-магазину с помощью Elasticsearch

## Быстрый старт

- Разверните Elasticsearch в Docker: `docker compose up --build -d`.
- Установите автозагрузку: `composer install`.
- Запустите пример: `php bin/console app:hello --name="Your Name"`.
- Короткий запуск: `./cli app:hello --name="Your Name"`.
- Список доступных команд: `./cli app:help`.

## Примеры доступных команд

- Проверка доступности ES: `./cli app:es-ping`.
- Bulk импорт NDJSON: `./cli app:es-bulk --file=./data/books.json`.
- Удаление индекса: `./cli app:es-drop-index --name=otus-shop`.
- Поиск: `./cli app:es-search --q="рыцОри" --category="Исторический роман" --price-lt=2000 --in-stock=1 --size=10`.

## Структура

```
bin/        # точка входа console
config/     # регистрация команд
app/        # код приложения (Console)
```

## Добавление команд

- Создайте класс, реализующий `App\Console\Command\CommandInterface` в `app/Console/Command`.
- Зарегистрируйте команду (экземпляр) в `config/console.php`.
