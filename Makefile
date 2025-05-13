# Makefile для проекта hw02-bash

# 🚀 Запустить контейнер в фоне
up:
	docker compose up -d

# 🧹 Остановить и удалить контейнер
down:
	docker compose down

# 🔄 Пересобрать контейнер полностью
rebuild:
	docker compose build --no-cache

# 💻 Запустить контейнер и сразу войти в интерактивный bash
run:
	docker compose up -d && docker compose exec bash bash

# 🐚 Просто войти в bash внутри контейнера
shell:
	docker compose exec bash bash

# 📦 Установить зависимости (если будут в будущем)
install:
	docker compose exec bash apk add --no-cache bash coreutils bc

# 🧪 Примеры вызова скриптов
sum:
	docker compose exec bash sum $(a) $(b)

top-cities:
	docker compose exec bash top-cities /app/data/users.txt
