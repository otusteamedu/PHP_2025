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

c-i:
	composer install
c-v:
	composer validate
c-r:
	composer require $(filter-out $@,$(MAKECMDGOALS))
c-rm:
	composer remove $(filter-out $@,$(MAKECMDGOALS))
c-r-dev:
	composer require --dev $(filter-out $@,$(MAKECMDGOALS))
c-d-a:
	composer dump-autoload
c-test:
	composer test
cest-build:
	composer cest-build
cest-run:
	composer cest-run
