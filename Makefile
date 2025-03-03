include .env

up:
	@docker compose up -d

down:
	@docker compose down

rebuild:
	@docker compose up -d --no-deps --build

logs:
	@docker compose logs

logs_nginx:
	@docker compose logs --follow --tail=10 nginx

console_f:
	@docker compose exec app_f bash

console_s:
	@docker compose exec app_s bash

console_r:
	@docker compose exec app_r bash