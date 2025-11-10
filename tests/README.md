# Tests for Email Validator

Этот проект использует Codeception для тестирования с помощью четырех типов тестов:
1. Unit Tests - для тестирования отдельных классов и методов
2. API Tests - для тестирования конечных точек REST API
3. Functional Tests - для функционального тестирования приложения через эмуляцию веб-запросов
4. Acceptance Tests - для тестирования пользовательского интерфейса с Selenium WebDriver

## Настройка

Убедитесь, что у вас установлены все зависимости:
```shell
composer install
```

## Запуск тестов

### Запустить все тесты:
```shell
./vendor/bin/codecept run --verbose
```

### Запуск определенных наборов тестов:

1. Unit Tests:
```shell
./vendor/bin/codecept run Unit --verbose
```

2. API Tests:
```shell
./vendor/bin/codecept run Api  --verbose
```

3. Functional Tests:
```shell
./vendor/bin/codecept run Functional  --verbose
```

4. Acceptance Tests:
```shell
./vendor/bin/codecept run Acceptance  --verbose
```

### Запуск определенных тестовых файлов:

```shell
./vendor/bin/codecept run tests/Unit/EmailVerifierTest.php
./vendor/bin/codecept run tests/Api/EmailValidationCest.php
./vendor/bin/codecept run tests/Functional/EmailValidationCest.php
./vendor/bin/codecept run tests/Acceptance/EmailValidationCest.php
```

## Конфигурация

- [codeception.yml](../codeception.yml) - Основной конфигурационный файл
- [unit.suite.yml](./Unit.suite.yml) - Конфигурация набора модульных тестов
- [api.suite.yml](./Api.suite.yml) - Конфигурация набора тестов API
- [acceptance.suite.yml](./Acceptance.suite.yml) - Конфигурация набора приемочных тестов
- [functional.suite.yml](./Functional.suite.yml) - Конфигурация функционального набора тестов
