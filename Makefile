build:
	@docker compose build app

up:
	@docker compose up -d

down:
	@docker compose down

console:
	@docker compose exec app bash
