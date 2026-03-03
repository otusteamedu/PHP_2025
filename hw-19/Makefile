SHELL := /bin/bash
.DEFAULT_GOAL := help
.PHONY: *

IN_CONTAINER := $(if $(wildcard /.dockerenv),1,0)
FORBID_IN_CONTAINER = [[ "$(IN_CONTAINER)" != "1" ]] || (echo "Not allowed inside container" && exit 1)

ifeq ($(IN_CONTAINER),1)
  CMD_EXEC :=
else
  APP_CONTAINER := app
  CMD_EXEC := docker compose exec $(APP_CONTAINER)
endif

terminal:
	@docker compose exec -it app bash

start:
	@printf "\033[0;36mStarting environment ...\033[0m\n"
	@$(FORBID_IN_CONTAINER)
	@docker compose up -d
	@$(CMD_EXEC) composer install

stop:
	@printf "\033[0;36mStoping environment ...\033[0m\n"
	@$(FORBID_IN_CONTAINER)
	@docker compose down

start-consumer:
	@printf "\033[0;36mStarting consumer ...\033[0m\n"
	@$(CMD_EXEC) php public/index.php start-bank-statement-consumer
