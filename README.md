## Описание выполненного домашнего задания №21

Система автоматического деплоя PHP приложения [media-monitoring-system](https://github.com/Andrey-Yurchuk/media-monitoring-system)
с использованием GitHub Actions, Self-hosted Runner и Blue-Green Deployment

### Основной файл системы деплоя

- `.github/workflows/deploy.yml` - GitHub Actions workflow

### Как это работает

#### 1. Blue-Green Deployment (Zero-Downtime)
- **Триггер:** Push в ветку `main`
- **Процесс:**
    1. GitHub Actions запускается автоматически
    2. Self-hosted Runner получает задачу
    3. Определяется текущее активное окружение (Blue или Green)
    4. Запускается Load Balancer (если не запущен)
    5. Останавливается старое окружение
    6. Запускается новое окружение в неактивном слоте
    7. Выполняется health check нового окружения
    8. Load Balancer переключается на новое окружение
    9. Очищаются старые Docker образы

#### 2. Конфигурация сред
- **Разработка:** `docker-compose.yml`
- **Blue окружение:** `docker-compose.blue.yml`
- **Green окружение:** `docker-compose.green.yml`
- **Load Balancer:** `docker-compose.lb.yml`

### Структура деплоя
```
/var/www/html/
├── media-monitoring-system/               # Исходный код
├── production-media-monitoring/           # Продакшн среда
├── actions-runner/                        # GitHub Runner
└── backup-media-monitoring-*/             # Бэкапы
```