dc-create-env:
	cp .env.docker.example .env.docker
	@echo "!-> Need changed .env.docker <-!"

dc-build:
	docker compose --env-file .env.docker build

dc-up:
	docker compose --env-file .env.docker up

dc-up-d:
	docker compose --env-file .env.docker up -d

dc-down:
	docker compose --env-file .env.docker down -v

dc-stop:
	docker compose --env-file .env.docker stop
