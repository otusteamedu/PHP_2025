# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus


# Описание

Написал простое приложение, которое отправляет запрос с датами в очередь

Форма принимает на вход дату начала, дата конца (например даты выписки) и email, на который должно упасть уведомление

## Запуск

Для запуска необходимо заполнить .env файл по примеру .env.example

Данные для подключения к RabbitMQ из docker-compose.yaml
```dotenv
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
```

Данные для подключения почты можно получить по инструкциям https://yandex.ru/support/yandex-360/business/mail/ru/mail-clients/others
или https://support.google.com/a/answer/176600?hl=ru (настройка только по SMPT протоколу)

После заполнения всех необходимых полей запустить сборку контейнеров командой
```
docker compose up
```

В контейнере phpnew будут обрабатываться запросы от браузера
receiver будет работать в контейнере php-receiver

По адресу http://localhost:80/ будет доступна форма для создания запроса и отправки его на обработку.
Обработчик просто считывает запрос из очереди и возвращает сообщение, что запрос с #ID# обработан успешно.

