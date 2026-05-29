.PHONY: setup migrate test test-coverage test-coverage-check stan cs-fix

# Минимальный порог покрытия строк (в процентах)
COVERAGE_MIN ?= 70

# Полная настройка: миграции + топология RabbitMQ
setup: migrate
	@echo "Настройка топологии RabbitMQ..."
	php bin/setup-rabbitmq.php

# Выполнение SQL-миграций
migrate:
	@echo "Выполнение миграций БД..."
	php bin/migrate.php

# Запуск тестов
test:
	php vendor/bin/phpunit

# Запуск тестов с покрытием (clover XML-отчёт)
test-coverage:
	php vendor/bin/phpunit --coverage-clover coverage.xml

# Запуск тестов с проверкой минимального порога покрытия строк
test-coverage-check: test-coverage
	php bin/check-coverage.php coverage.xml $(COVERAGE_MIN)

# PHPStan — статический анализ
stan:
	php vendor/bin/phpstan analyse

# PHP-CS-Fixer — исправление стиля кода
cs-fix:
	php vendor/bin/php-cs-fixer fix src/ tests/
