# Сборка образа для GitHub-варианта
build-github:
	docker compose -f docker-compose.github.yml build

# Сборка образа для Packagist-варианта
build-packagist:
	docker compose -f docker-compose.packagist.yml build

# Скачивание видео через пакет, подключенный с GitHub
download-github:
ifndef URL
	$(error Укажите переменную URL: make download-github URL="<ссылка>")
endif
	docker compose -f docker-compose.github.yml run --rm github php github-run.php "$(URL)"

# Скачивание видео через пакет, подключенный с Packagist
download-packagist:
ifndef URL
	$(error Укажите переменную URL: make download-packagist URL="<ссылка>")
endif
	docker compose -f docker-compose.packagist.yml run --rm packagist php packagist-run.php "$(URL)"
