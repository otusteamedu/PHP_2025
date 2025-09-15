<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Connect\Connect;

$sql = <<<SQL
        CREATE TABLE "user" (
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(25) NOT NULL,
            created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL,
            id SERIAL PRIMARY KEY
        );
    SQL;

$pdo = new Connect()->connect();
$pdo->exec($sql);

echo "Таблица создана успешно.";
