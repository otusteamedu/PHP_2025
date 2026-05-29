<?php

declare(strict_types=1);

/**
 * Скрипт выполнения SQL-миграций
 * Запуск: php bin/migrate.php
 * Или через make migrate
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Infrastructure\Persistence\MigrationRunner;

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $migrationRunner = $container->get(MigrationRunner::class);
    $count = $migrationRunner->run();

    echo "Миграции выполнены успешно. Применено: {$count}\n";
} catch (\Throwable $e) {
    echo "Ошибка выполнения миграций: " . $e->getMessage() . "\n";
    exit(1);
}
