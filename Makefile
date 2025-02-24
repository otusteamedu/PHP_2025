up:
	@docker compose up -d

down:
	@docker compose down

console 1:
	@docker compose exec app_1 bash

console 2:
	@docker compose exec app_2 bash

console 3:
	@docker compose exec app_3 bash