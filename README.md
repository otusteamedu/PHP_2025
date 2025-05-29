
---

## Установка и запуск

### 1. Установка зависимостей

```bash
composer install
```
### 2. Установка и запуск RabbitMQ

```bash
docker run -d --hostname my-rabbit --name rabbitmq -p 5672:5672 -p 15672:15672 rabbitmq:3-management
```
Для управления RabbitMQ перейдите в браузере по адресу:
http://localhost:15672/
(вход по умолчанию: login = guest, password = guest)


### 3. Запуск локального веб-сервера

```bash
php -S localhost:8000 -t public
```

### 4. Запуск воркера

```bash
php worker.php
```

Для доступа к приложению перейдите в браузере по адресу:
http://localhost:8000/
