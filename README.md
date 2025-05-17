# 🎩 Домашнее задание 3: Работа с пакетами (Composer Package)

## 🌟 Цель

Научиться создавать собственные PHP-пакеты и подключать их к проектам с помощью Composer как внешние зависимости.

## 📆 Описание

В рамках задания:

1. Создан пакет [`vmatkovskii/video-dl`](https://github.com/vmatkovskii/video-dl).
2. Соблюден PSR-4.
3. Пакет опубликован на GitHub и Packagist.
4. Созданы два отдельных контейнера:

    * для GitHub-подключения (dev-main)
    * для Packagist (стабильный тег ^1.0)

## 📂 Структура

```
otus-php-hw03/
├── downloads/
│   ├── github/
│   └── packagist/
├── github/
│   ├── composer.json
│   ├── Dockerfile
│   └── github-run.php
├── packagist/
│   ├── composer.json
│   ├── Dockerfile
│   └── packagist-run.php
├── docker-compose.github.yml
├── docker-compose.packagist.yml
├── Makefile
├── .gitignore
└── README.md
```

## 🔢 Запуск пакета

### 🔗 Через GitHub (dev-main)

**`github/composer.json`\*\*\*\*:**

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/vmatkovskii/video-dl.git"
    }
  ],
  "require": {
    "vmatkovskii/video-dl": "dev-main"
  }
}
```

**Запуск вручную:**

```bash
docker compose -f docker-compose.github.yml build
docker compose -f docker-compose.github.yml run --rm github php github-run.php "<URL>"
```

**Или через Makefile:**

```bash
make build-github
make download-github URL="<URL>"
```

---

### 📆 Через Packagist (^1.0)

**`packagist/composer.json`\*\*\*\*:**

```json
{
  "require": {
    "vmatkovskii/video-dl": "^1.0"
  }
}
```

> ✔️ Требуется наличие тега `v1.0.0` в репозитории.

**Запуск вручную:**

```bash
docker compose -f docker-compose.packagist.yml build
docker compose -f docker-compose.packagist.yml run --rm packagist php packagist-run.php "<URL>"
```

**Или через Makefile:**

```bash
make build-packagist
make download-packagist URL="<URL>"
```

---

## 📥 Установка в проект

```bash
# Стабильная версия через Packagist
composer require vmatkovskii/video-dl:^1.0

# По GitHub (dev-main)
composer require vmatkovskii/video-dl:dev-main --prefer-source
```

---

## ✅ Требования

* Пакет подключается через Composer
* Соответствие PSR-4
* Оба варианта (с GitHub и Packagist) работают

---

📘 *Ссылка на GitHub-репозиторий пакета: **[https://github.com/vlavlamat/video-dl](https://github.com/vlavlamat/video-dl)**.

📘 *Ссылка на Packagist пакета: **[https://packagist.org/packages/vmatkovskii/video-dl](https://packagist.org/packages/vmatkovskii/video-dl)**.
