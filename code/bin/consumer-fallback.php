<?php

declare(strict_types=1);

/**
 * Точка входа consumer-а очереди mkd.fallback (DLQ)
 * Запускается через Supervisor
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Infrastructure\Queue\FallbackConsumer;

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $consumer = $container->get(FallbackConsumer::class);
    $consumer->consume();
} catch (\Throwable $e) {
    echo "Фатальная ошибка consumer-fallback: " . $e->getMessage() . "\n";
    exit(1);
}
