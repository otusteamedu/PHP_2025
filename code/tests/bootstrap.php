<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenvPath = __DIR__ . '/..';
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->safeLoad();

    // Dotenv v5 записывает в $_ENV/$_SERVER, но не в putenv() — добавляем для совместимости с getenv()
    foreach ($_ENV as $key => $value) {
        if (getenv($key) === false) {
            putenv("$key=$value");
        }
    }
}
