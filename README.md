Инструкция по запуску
1. Клонирование и настройка
# Создайте директорию проекта и перейдите в неё
mkdir statement-queue && cd statement-queue
# Поместите все файлы в соответствующие директории

2. Запуск приложения
# Запуск всех сервисов
docker-compose up -d
# Просмотр логов
docker-compose logs -f
# Запуск worker в отдельном терминале
docker-compose exec php php worker.php

3. Проверка работы
Веб-интерфейс: Откройте http://localhost:8080
RabbitMQ Management: http://localhost:15672 (guest/guest)
Отправка тестового запроса: Заполните форму и отправьте запрос
