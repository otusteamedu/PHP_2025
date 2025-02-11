up:
	@docker compose up -d

down:
	@docker compose down

.PHONY: console
console:
	@docker compose exec app bash