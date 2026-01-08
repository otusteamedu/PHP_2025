<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Services\PostgresService;

$migrationName = '001_create_movie_table';

$columns = [
    'id' => 'SERIAL PRIMARY KEY',
    'title' => 'VARCHAR(255) NOT NULL',
    'duration' => 'INT NOT NULL',
    'description' => 'TEXT'
];

try {
    $db = new PostgresService();
    $db->createTable('movie', $columns);
    echo "[OK] {$migrationName} applied" . PHP_EOL;
} catch (\PDOException $e) {
    echo "[ERROR] {$migrationName} failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
