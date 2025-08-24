### Описание выполненного домашнего задания №14

В рамках задания был проведен анализ исходного кода [приложения для верификации e‑mail‑адресов](https://github.com/otusteamedu/PHP_2025/tree/ayurchuk/hw5) и выполнен рефакторинг с применением принципов Clean Architecture и Domain-Driven Design (DDD). В результате была создана трехуровневая архитектура с четким разделением ответственностей, внедрены доменные сущности, use cases, DTO объекты и DI-контейнер.


#### Функциональность

- Проверка email по регулярным выражениям
- Проверка DNS MX-записей
- Валидация списка email адресов
- Поддержка GET и POST запросов

#### Запуск приложения

Для запуска приложения выполните:
```bash
docker-compose build && docker-compose up -d
```

## Локальное тестирование приложения

### 1. Валидация корректных email адресов
```bash
curl "http://localhost:8080/?emails=test@example.com,user@gmail.com,admin@google.com"
```

### 2. Валидация некорректных email адресов
```bash
curl "http://localhost:8080/?emails=invalid-email,test@,user@"
```

### 3. Смешанная валидация
```bash
curl "http://localhost:8080/?emails=test@example.com,invalid-email,user@gmail.com"
```

### 4. POST запрос
```bash
curl -X POST -d "emails=admin@google.com,test@nonexistentdomain.xyz" "http://localhost:8080/"
```

### 5. Пустой запрос (обработка ошибки)
```bash
curl "http://localhost:8080/"
```