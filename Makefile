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
