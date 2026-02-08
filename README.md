# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Запуск проекта

```bash
docker-compose up -d
```

## Тестирование

### Unit тесты (PHPUnit)

```bash
# Все unit тесты
docker-compose exec -w /data/mysite.local app composer test

# С покрытием кода (HTML отчёт в coverage/)
docker-compose exec -w /data/mysite.local app composer test-coverage
```

### Функциональные тесты / API тесты (Codeception)

```bash
# Запуск API тестов
docker-compose exec -w /data/mysite.local app composer test-api
```
