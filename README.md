# PHP_2025 — Система запроса банковских выписок

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Описание

Асинхронная система запроса фейковых банковских выписок с использованием RabbitMQ и email-уведомлений.

## Запуск

docker-compose up -d --build

## Компоненты

Web-форма Форма запроса выписки - GET http://localhost
API Обработка POST-запросов - POST http://localhost
RabbitMQ Управление очередями (guest/guest) - http://localhost:15672
MailHog Просмотр отправленных писем - http://localhost:8025

## Как пользоваться

1. Откройте http://localhost
2. Заполните форму (ФИО, Email, Год выписки)
3. Нажмите "Запросить выписку"
4. Запрос попадает в очередь RabbitMQ
5. Worker обрабатывает запрос и отправляет email
6. Просмотрите письмо в MailHog: http://localhost:8025

## Перезапуск воркера

docker-compose restart worker

## Остановка всех сервисов

docker-compose down
