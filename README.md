# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Домашнее задание к уроку 14. Redis

**Цель:**

Научиться взаимодействовать с Redis.

**Описание/Пошаговая инструкция выполнения домашнего задания:**

Аналитик хочет иметь систему со следующими возможностями:

1. Система должна хранить события, которые в последующем будут отправляться сервису событий
2. События характеризуются важностью (аналитик готов выставлять важность в целых числах)
3. События характеризуются критериями возникновения. Для простоты все критерии заданы так: <критерий>=<значение>

**Написать систему, которая будет уметь:**

1. добавлять новое событие в систему хранения событий
2. очищать все доступные события
3. отвечать на запрос пользователя наиболее подходящим событием
4. использовать для хранения событий redis

**Критерии оценки:**

1. Желательно параллельно попрактиковаться и выполнить ДЗ в других NoSQL хранилищах
2. Слой кода, отвечающий за работу с хранилищем должен позволять легко менять хранилище.

## Описание разработанного приложения

* Приложение реализовано в виде веб-приложения с REST API.
* Приложение по запросу пользователя ищет событие с наибольшим рейтингом, в котором выполняются одновременно все условия
  запроса пользователя.
* Приложение поддерживает использование Redis и ElasticSearch в качестве хранилищ. Переключение между хранилищами
  осуществляется в файле конфигурации приложения config/config.php

## REST API приложения

### Удаление всех доступных событий:

```
curl --location --request DELETE 'http://localhost/storage'
```

### Добавление новых событий в систему:

```
curl --location 'http://localhost/events/' \
--header 'Content-Type: application/json' \
--data '{
    "priority": 1000,
    "conditions": {
        "param1": 1,
        "param2": 2,
        "param3": 3
    },
    "event": {
        "id": 1,
        "name": "Event 1"
    }
}'
```

```
curl --location 'http://localhost/events/' \
--header 'Content-Type: application/json' \
--data '{
    "priority": 2000,
    "conditions": {
        "param1": 2,
        "param2": 2
    },
    "event": {
        "id": 2,
        "name": "Event 2"
    }
}'
```

```
curl --location 'http://localhost/events/' \
--header 'Content-Type: application/json' \
--data '{
    "priority": 3000,
    "conditions": {
        "param1": 1,
        "param2": 2
    },
    "event": {
        "id": 3,
        "name": "Event 3"
    }
}'
```

```
curl --location 'http://localhost/events/' \
--header 'Content-Type: application/json' \
--data '{
    "priority": 500,
    "conditions": {
        "param2": 2,
        "param3": 3
    },
    "event": {
        "id": 4,
        "name": "Event 4"
    }
}'
```

### Поиск наиболее подходящего под запрос пользователя события:

**Запрос #1**

```
curl --location --request GET 'http://localhost/events/' \
--header 'Content-Type: application/json' \
--data '{
    "params": {
        "param1": 1,
        "param2": 2
    }
}'
```

**Результат запроса #1**

```
{
    "priority": 3000,
    "event": {
        "id": 3,
        "name": "Event 3"
    },
    "conditions": {
        "param1": "1",
        "param2": "2"
    }
}
```

**Запрос #2**

```
curl --location --request GET 'http://localhost/events/' \
--header 'Content-Type: application/json' \
--data '{
    "params": {
        "param2": 2,
        "param3": 3
    }
}'
```

**Результат запроса #2:**

```
{
    "priority": 1000,
    "event": {
        "id": 1,
        "name": "Event 1"
    },
    "conditions": {
        "param1": "1",
        "param2": "2",
        "param3": "3"
    }
}
```
