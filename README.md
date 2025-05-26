# 🧹 Домашнее задание №5 — Валидация скобок и балансировка нагрузки

## 📦 Описание проекта

Учебный проект для отработки практических навыков в работе с Docker, PHP-FPM, Nginx, Redis и фронтендом на Vue.js.
Основная задача — реализовать веб-сервис, принимающий строку из скобок через POST-запрос, проверяющий её корректность с точки зрения вложенности и симметрии, а также организовать балансировку нагрузки между несколькими backend-контейнерами.

Проект построен на микросервисной архитектуре с разделением на:

✅ балансировщик (nginx upstream)
✅ два backend-а (nginx + php-fpm)
✅ Redis-хранилище для сессий
✅ frontend-приложение (Vue + Vite), предоставляющее веб-форму для проверки

## 🧱 Стек технологий

* PHP 8.3 (FPM)
* Nginx (балансировщик и backend)
* Redis
* Docker / Docker Compose
* Composer (PSR-4 autoload)
* Vue 3 + Vite

## 📁 Структура проекта

```
otus-php-hw04/
├── balancer/
│   └── nginx.conf
├── docker/
│   ├── balancer/
│   │   └── balancer.Dockerfile
│   ├── frontend/
│   │   ├── vue.dev.Dockerfile
│   │   ├── vue.prod.Dockerfile
│   │   └── nginx.conf
│   ├── nginx/
│   │   └── nginx.Dockerfile
│   └── php/
│       ├── php.Dockerfile
│       ├── php.ini
│       ├── php-fpm.conf
│       ├── conf.d/
│       │   └── session.redis.ini
│       ├── www-fpm1.conf
│       └── www-fpm2.conf
├── frontend/
│   ├── src/
│   │   ├── utils/
│   │   │   └── bracketGenerator.js
│   │   ├── App.vue
│   │   └── main.js
│   ├── index.html
│   ├── vite.config.js
│   └── package.json
├── public/
│   └── index.php
├── src/
│   └── Validator.php
├── vendor/
├── .gitignore
├── composer.json
├── docker-compose.yml
├── docker-compose.dev.yml
├── docker-compose.prod.yml
├── Makefile
└── README.md
```

## ⚙️ Как запустить проект

### Dev-режим (сборка и запуск всех сервисов с исходниками)

```bash
make dev-build    # Сборка и запуск
make dev-down     # Остановка dev-окружения
```

### Prod-режим (сборка и запуск production-образов)

```bash
make prod-up      # Подтянуть и запустить production-образы
make prod-down    # Остановка prod-окружения
```

### Полный список команд в Makefile

* `make dev-up` — поднять dev-окружение
* `make dev-down` — остановить dev-окружение
* `make dev-build` — собрать и запустить dev-окружение с dev-зависимостями
* `make dev-rebuild` — пересобрать dev-окружение без кеша
* `make dev-logs` — показать dev-логи
* `make prod-up` — подтянуть образы и запустить prod-окружение
* `make prod-down` — остановить prod-окружение
* `make prod-build-local` — локально собрать prod-образы
* `make prod-push` — запушить prod-образы в Docker Hub
* `make prod-logs` — показать prod-логи
* `make ps` — показать запущенные контейнеры

## 🧪 Проверка работы

Всё тестируется **через веб-интерфейс**, сделанный на Vue.
Он доступен по адресу [http://localhost](http://localhost), где отображается форма:
🔸 введите строку из скобок (или сгенерируйте пример)
🔸 отправьте на проверку
🔸 получите результат прямо в браузере.

## 🔍 Валидация строки

Валидация реализована в `src/Validator.php` и учитывает:

* пустые строки → ошибка
* корректность вложенности `(` и `)`
* запрет любых других символов

Результат обработки:

* ✅ 200 OK, если строка валидна
* ❌ 400 Bad Request, если строка некорректна

## 🌐 Архитектура Nginx

Балансировщик (балансирует по backend):

```nginx
upstream backend_upstream {
    server nginx-backend1:80;
    server nginx-backend2:80;
}

location /api/ {
    proxy_pass http://backend_upstream/;
    ...
}
```

Backend-сервисы (nginx + php-fpm через сокет).

## 🛡️ Сессии и Redis

Сессии PHP сохраняются в Redis с помощью настроек:

```
session.save_handler = redis
session.save_path = "tcp://redis-node1:6379?prefix=otus_hw04:"
```

## ✅ Выполненные требования

* [x] Docker-контейнеры: nginx, php-fpm, redis
* [x] POST-запрос `/api/validate`
* [x] Корректная валидация вложенных скобок
* [x] Балансировка между backend-ами через nginx upstream
* [x] Redis-сессии
* [x] Разделение dev и prod окружений
* [x] Frontend-интерфейс на Vue

---

## 📮 Автор

**Vladimir Matkovskii** — [vlavlamat@icloud.com](mailto:vlavlamat@icloud.com)
