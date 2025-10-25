## Инструкция
1. В корне произвести команду docker compose build
2. Запустить контейнеры docker compose up -d
3. Зайти в контейнер app -> docker compose exec app bash
4. В контейнере app произвести подтягивание зависимостей composer install
5. Проверяем запросы /api/v1/event. Подробнее указано в файле data.yaml в корне
6. В post /api/v1/event создается очередь в rabbitmq
7. Прослушка совершается командой в контейнере приложения php ./public/consumer.php
8. Чтобы сгенерить yaml файл php public/swagger.php