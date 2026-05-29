<?php

declare(strict_types=1);

/**
 * Точка входа consumer рассылки новостей
 * Запускается через supervisor
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Infrastructure\Queue\NewsDeliveryConsumer;

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $consumer = $container->get(NewsDeliveryConsumer::class);
    $consumer->consume();
} catch (\Throwable $e) {
    echo "Фатальная ошибка consumer-news-delivery: " . $e->getMessage() . "\n";
    exit(1);
}
