start:
	docker-compose down --remove-orphans
	docker-compose build --no-cache
	docker-compose up -d
	sleep 5
	docker exec app bash -c "cd /opt/project && composer require otus/url-helper @dev"
	@echo "Проект запущен!"

test:
	docker exec app php /data/application.local/app/index.php

## Запустить
up:
	docker-compose up -d

## Остановить
stop:
	docker-compose stop

## Открыть в браузере
open:
	@echo "Откройте в браузере: http://localhost:8080"

## Тест через браузер (curl)
test-web:
	curl http://localhost:8080