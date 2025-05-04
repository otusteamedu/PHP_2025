COMPOSE = docker compose --env-file .env.docker

dc-create-env:
	cp .env.docker.example .env.docker
	@echo "!-> Need changed .env.docker <-!"

dc-build:
	${COMPOSE} build

dc-up:
	${COMPOSE} up

dc-up-d:
	${COMPOSE} up -d

dc-down:
	${COMPOSE} down -v

dc-stop:
	${COMPOSE} stop
