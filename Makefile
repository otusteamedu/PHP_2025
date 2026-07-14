TARGETS := status up down start stop directory-tree
.PHONY: $(TARGETS)

CMD_TARGETS := $(MAKECMDGOALS)
ALLOWED_TARGETS := $(filter $(TARGETS),$(CMD_TARGETS))

ifneq ($(ALLOWED_TARGETS),$(CMD_TARGETS))
$(error Обнаружены недопустимые цели: $(filter-out $(ALLOWED_TARGETS),$(CMD_TARGETS)). Доступные цели: $(TARGETS))
endif

status:
	docker compose ps -a

up:
	docker compose -f docker-compose.balancer.yml up -d

down:
	docker compose -f docker-compose.balancer.yml down

start:
	docker compose -f docker-compose.balancer.yml start

stop:
	docker compose -f docker-compose.balancer.yml stop

directory-tree:
	docker compose -f docker-compose.balancer.yml exec php_1 php bin/console.php app:directory:tree $(path) $(depth)
