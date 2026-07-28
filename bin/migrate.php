<?php

declare(strict_types=1);

use App\Application\UseCases\CreateTaskTableUseCase;
use App\Infrastructure\Database\Config\DatabaseConfigLoader;
use App\Infrastructure\Database\Connection\ConnectionFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$config = DatabaseConfigLoader::load();

$pdo = ConnectionFactory::create($config);

(new CreateTaskTableUseCase($pdo))->execute();

echo "Database migrated successfully.\n";
