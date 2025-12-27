# OTUS console application PHP

Поиск по книжному интернет-магазину с помощью Elasticsearch

## Быстрый старт

- Разверните Elasticsearch в Docker: `docker compose up --build -d`.
- Установите автозагрузку: `composer install`.
- Запустите пример: `php bin/console app:hello --name="Your Name"`.
- Короткий запуск: `./cli app:hello --name="Your Name"`.

## Структура

```
bin/        # точка входа console
config/     # регистрация команд
app/        # код приложения (Console)
```

## Добавление команд

- Создайте класс, реализующий `App\Console\Command\CommandInterface` в `app/Console/Command`.
- Зарегистрируйте команду (экземпляр) в `config/console.php`.
