# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

Домашнее задание по лекции 29 Очереди. Часть 2.  Работа с очередью

## Инструкции по развёртыванию системы
1. В корне проекта созадаем `.env` по образцу `.env.example` и прописываем доступы.   

2. Устанавливаем зависимости:
```bash
composer install
```
3. Запускаем контейнера:
```bash
docker-compose up -d --build
```
Если все запустилось правильно, то должны быть доступны
UI c формой заявок: http://localhost:8000
RabbitMQ UI: http://localhost:15672 (по дефолту: guest / guest)
MailHog: http://localhost:8025

4. В новом терминале запускаем воркера для чтения из очереди
```bash
docker compose exec php php worker/worker.php
```

5. Все, можно проверять.
    - Перейдите на http://localhost:8000
    - Отправьте форму
    - В консоли должно появиться сообщение от воркера о начале обработки
    - Через  примерно 10 секунд:
    - в statuses/statuses.json будет "done"
    - в MailHog появится email
    - в Telegram бота придет сообщение