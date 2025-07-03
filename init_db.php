<?php
declare(strict_types=1);

$path = getenv('SQLITE_PATH') ?: __DIR__ . '/data/reports.sqlite';

if (!file_exists(dirname($path))) {
    mkdir(dirname($path), 0777, true);
}

$pdo = new PDO("sqlite:" . $path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS reports (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        status TEXT NOT NULL DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )"
);

echo "Database initialized at $path\n";
