up:
	@docker compose up -d

down:
	@docker compose down

console_1:
	@docker compose exec app_1 bash

console_2:
	@docker compose exec app_2 bash

console_3:
	@docker compose exec app_3 bash