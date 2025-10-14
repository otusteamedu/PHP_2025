

# Приложение обработки отложенных запросов (банковская выписка)

Учебное приложение, принимает заявки на генерацию банковской выписки за выбранный период, ставит их в очередь и 
асинхронно обрабатывает. После готовности «генерации» отправляется уведомление пользователю (эмуляция), 
а результат логируется в БД.

## Стек
- PHP 8.2
- Slim Framework 4
- RabbitMQ
- PostgreSQL (локально установлен на вашей машине)
- Nginx + PHP‑FPM (в Docker)

## Структура проекта
- `public/` — точка входа (index.php) и статические файлы
- `src/` — код приложения (Controllers, Services, Repositories, config)
- `bin/` — CLI‑скрипты обработчиков очередей
- `docker/` — конфигурации Docker (nginx, php-fpm)
- `sql/` — SQL‑скрипты для создания таблиц

## Подготовка окружения
  `psql -h localhost -U postgres -d postgres -f ./sql/init.sql`

1. Запуск Docker‑сервисов (nginx, php‑fpm, rabbitmq)
    - Выполните: `docker compose up -d --build`
    - Откройте RabbitMQ Management в браузере: http://localhost:15672 (логин/пароль: guest/guest)
    - Приложение будет доступно на: http://localhost:8080

2. Установка PHP‑зависимостей
    - Контейнер php-fpm в Dockerfile уже вызывает `composer install`. Если нужно выполнить вручную:
    - Внутри контейнера: `docker exec -it php2025-php composer install`

## Работа с приложением
1. Откройте http://localhost:8080
2. Заполните форму: Email, Дата начала, Дата окончания; отправьте. 
3. Страница подтвердит приём заявки и сообщит её номер. Обработка идёт асинхронно.

## Обработчики очередей
Обработчики запускаются как отдельные процессы. Можно запускать их внутри контейнера php‑fpm.

- Обработчик заявок (читает `orders.queue`, имитирует генерацию, ставит задачу на уведомление):
  - `docker exec -it php2025-php php bin/order-consumer.php`

- Обработчик уведомлений (читает `notifications.queue`, «шлёт email», пишет лог в БД):
  - `docker exec -it php2025-php php bin/notification-consumer.php`

Вывод в консоль покажет обработанные сообщения.

## Переменные окружения (.env)
Пример — в `.env.example`:
- DB_HOST=host.docker.internal
- DB_PORT=5432
- DB_NAME=postgres
- DB_USER=postgres
- DB_PASSWORD=postgres

- RABBITMQ_HOST=rabbitmq
- RABBITMQ_PORT=5672
- RABBITMQ_USER=guest
- RABBITMQ_PASS=guest

- MAIL_HOST=localhost
- MAIL_PORT=2525
- MAIL_SMTP_AUTH=1
- MAIL_USERNAME=
- MAIL_PASSWORD=
- MAIL_FROM=
- MAIL_FROM_NAME=

## Примечания
- Этот проект демонстрирует архитектуру отложенной обработки задач с очередями и микросервисным стилем исполнения обработчиков.
