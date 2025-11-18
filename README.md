# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

Домашнее задание по лекции 25 Практикум по тестированию . Пишем тесты.

В качестве приложения была взято ДЗ 5 - Приложение верификации email

Были написаны: 
1. Unit-тесты на основные функции
- Validator::isValidEmailFormat()
- Validator::hasValidMxRecord()
- Request::__construct()
- Processor::process()

2. Интеграционные тесты через curl

Команда для запуска тестов `vendor/bin/phpunit`


