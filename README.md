## Описание выполненного домашнего задания №21

Система автоматического деплоя PHP приложения [media-monitoring-system](https://github.com/Andrey-Yurchuk/media-monitoring-system) 
с использованием GitHub Actions и Self-hosted Runner

### Основной файл системы деплоя

- `.github/workflows/deploy.yml` - GitHub Actions workflow

### Как это работает

#### 1. Автоматический деплой
- **Триггер:** Push в ветку `main`
- **Процесс:**
    1. GitHub Actions запускается автоматически
    2. Self-hosted Runner получает задачу
    3. Останавливаются старые контейнеры
    4. Копируется новый код в продакшн папку
    5. Запускаются новые контейнеры
    6. Выполняется health check
    7. Очищаются старые Docker образы

#### 2. Конфигурация сред
- **Разработка:** `docker-compose.yml`
- **Продакшн:** `docker-compose.prod.yml`

### Структура деплоя
```
/var/www/html/
├── media-monitoring-system/               # Исходный код
├── production-media-monitoring/           # Продакшн среда
├── actions-runner/                        # GitHub Runner
└── backup-media-monitoring-*/             # Бэкапы
```
