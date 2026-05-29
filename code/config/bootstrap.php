<?php

declare(strict_types=1);

/**
 * Единая точка загрузки переменных окружения из .env
 * Подключается во всех entry points (index.php, bin/*.php)
 *
 * В Docker-окружении переменные уже переданы через env_file,
 * safeLoad() не перезаписывает существующие переменные.
 * В dev-окружении (локальный запуск) — загружает .env из корня проекта.
 */

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->safeLoad();
