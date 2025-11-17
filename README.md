# Приложение Валидатор email

## Подготовка рабочей среды
1. Запускает все сервисы, определённые в файле конфигурации docker-compose.yml. `docker compose up`
2. Устанавливаем зависимости `composer install`

## Пример запроса:

```bash
curl --location 'http://localhost:8080/validate/emails' \
--header 'Content-Type: application/json' \
--data-raw '["testusername@", "testdomainname", "testusername@testdomainname.testcountrycode", "testusername@testdomainname.ru"]'
```

## Пример ответа:
```json
{
    "testusername@": {
        "is_valid":false,
        "is_valid_format":false,
        "is_valid_dns":null,
        "errors": [
            "Invalid email format"
        ]
    },
    "testdomainname": {
        "is_valid":false,
        "is_valid_format":false,
        "is_valid_dns":null,
        "errors": [
            "Invalid email format"
        ]
    },
    "testusername@testdomainname.testcountrycode": {
        "is_valid":false,
        "is_valid_format":true,
        "is_valid_dns":false,
        "errors": [
            "No MX records found for domain"
        ]
    },
    "testusername@testdomainname.ru": {
        "is_valid":true,
        "is_valid_format":true,
        "is_valid_dns":true,
        "errors": []
    }
}
```
