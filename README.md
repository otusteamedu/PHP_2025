## Описание выполненного домашнего задания №19

Приложение представляет собой **Telegram Mini App** для генерации банковских выписок

### Получение токенов

#### Telegram Bot Token
1. Найдите @BotFather в Telegram
2. Отправьте команду `/newbot`
3. Следуйте инструкциям для создания бота
4. Сохраните полученный токен

#### Ngrok Auth Token
1. Зарегистрируйтесь на [ngrok.com](https://ngrok.com)
2. Получите Auth Token в разделе "Your Authtoken"
3. Скопируйте токен для настройки

### Установка и запуск

#### 1. Настройка переменных окружения
Создайте файл `.env` на основе `env.example`:
```bash
cp env.example .env
```
Заполните все необходимые переменные в файле `.env`.

#### 2. Запуск приложения
```bash
docker-compose build && docker-compose up -d

docker-compose exec php composer install
```

### Использование

1. Отправьте команду `/form` вашему боту
2. Нажмите кнопку "Открыть форму"
3. Заполните даты начала и окончания периода
4. Нажмите "Создать запрос выписки"
5. Получите результат в Telegram

**Примечание**: Webhook для Telegram бота устанавливается автоматически при запуске контейнеров

## Демонстрация работы приложения

Видео-демонстрация [смотреть](https://files.catbox.moe/xinzox.webm)

### Качество кода

Проект использует инструменты для обеспечения качества кода:

#### PHPStan
```bash
docker exec php-fpm vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=512M
```

#### PHP_CodeSniffer
```bash
docker exec php-fpm vendor/bin/phpcs --standard=phpcs.xml
```

