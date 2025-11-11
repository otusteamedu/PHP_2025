# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

Домашняя работа по лекции 14 Redis. 

Структура файлов
- docker - инфраструктура: Dockerfile и конфиги контейнеров
- public - точка входа 
- src/Controller - контроллер, обработка HTTP-запросов
- src/Factory - фабрика создания Repository
- src/Repository - интерфейс и реализации доступа к Redis и Elastic
- src/Service - бизнес-логика, вызов методов добавления, поиск и очистки
- templates - html файлы для фронта
- env - файл конфигурации: настройки доступа к бд и др.

Инструкции
1. docker-compose up -d
2. переходим на http://mysite.local/index.php
3. Добавление, удаление и очистка в соот. разделах.
4. Кнопка добавить условие для задания произвольного кол-ва параметров
