dc-build:
	docker compose --env-file .docker.env build

dc-up:
	docker compose --env-file .docker.env up

dc-up-d:
	docker compose --env-file .docker.env up -d

dc-down:
	docker compose --env-file .docker.env down -v

dc-stop:
	docker compose --env-file .docker.env stop
