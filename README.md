# 30 ФAPI

Научиться создавать универсальный интерфейс для различных потребителей (frontend фреймворки, мобильные приложения, сторонние приложения)


Описание/Пошаговая инструкция выполнения домашнего задания:

Необходимо реализовать Rest API с использованием очередей.

Ваши клиенты будут отправлять запросы на обработку, а вы будете складывать их в очередь и возвращать номер запроса.

В фоновом режиме вы будете обрабатывать запросы, а ваши клиенты периодически, используя номер запроса, будут проверять статус его обработки.

# Работа с приложением

`
docker compose up -d
`

`
docker compose exec php_fpm composer install
`

`
docker compose exec php_fpm php bin/migrate.php
`


Страница с rabbitmq management
http://localhost:15672/#/queues/%2F/tasks

Создать задачу POST `http://localhost:8080/tasks`

Получение статуса задачи GET `http://localhost:8080/tasks/2`


Продюсер для добавления задач в очередь можно запустить командой:

`
docker compose exec php_fpm php entry_point/producer.php -d
`

Воркер для выполнения задач можно запустить командой:

`
docker compose exec php_fpm php entry_point/worker.php -d
`
