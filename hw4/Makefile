## Копирует .env.dev.dist → .env (если .env не существует)
$(shell cp -n .env.dev.example .env)

## Загружает переменные из .env файла
## Экспортирует их в окружение make
include ./.env
export $(shell sed 's/=.*//' .env)

## Позволяет передавать аргументы командам в командной строке
RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
$(eval $(RUN_ARGS):;@:)

## Вызов команды make env
env:
	cp .env.${RUN_ARGS}.dist .env

## up: Запустить контейнеры в фоновом режиме
up:
	docker-compose -f docker-compose.yml up -d $(c)

## start: Запустить уже созданные контейнеры
start:
	docker-compose up -d

## stop: Остановить контейнеры
stop:
	docker-compose stop

## restart: Перезапустить контейнеры
restart:
	docker-compose -f docker-compose.yml restart $(c)

## down: Остановить и удалить контейнеры, сети, volumes
down-start:
	docker-compose -f docker-compose.yml down $(c)
	docker-compose build --no-cache
	docker-compose up -d

## build: Пересобрать Docker образы
build:
	docker-compose -f docker-compose.yml build $(c)

## logs: Показать логи контейнеров
logs:
	docker-compose -f docker-compose.yml logs -f $(c)

## ps: Показать статус контейнеров
ps:
	docker-compose -f docker-compose.yml ps
