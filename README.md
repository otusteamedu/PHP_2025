# Работа с очередью (HW#19)

## Запуск

### 1. Установка зависимостей

```bash
cd <<каталог_приложения>>/code
composer install
```

### 2. Настройка окружения

```bash
cd <<каталог_приложения>>
cp .env.example .env
cd code
cp .env.example .env
```

### 3. Запуск Docker-контейнеров

```bash
cd <<каталог_приложения>>
docker-compose up -d
```

### 4. Запуск консольного обработчика

```bash
cd <<каталог_приложения>>
docker-compose exec app php /data/mysite.local/bin/worker.php
```

### 5. Прописать DNS-запись: /etc/hosts (Linux/MacOS) | C:\Windows\System32\drivers\etc\hosts (Windows):

```
127.0.0.1 mysite.local
```

## Доступ к сервисам

| Сервис | URL | Описание |
|--------|-----|----------|
| Веб-приложение | http://mysite.local | Форма запроса выписки |
| RabbitMQ Management | http://localhost:15672 | guest/guest |
| MailHog | http://localhost:8025 | Просмотр отправленных email |
