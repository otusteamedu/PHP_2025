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

console:
	@docker compose exec app bash