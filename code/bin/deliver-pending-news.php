#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Скрипт рассылки накопившихся pending-новостей
 * Запускается по расписанию (через Supervisor) каждую минуту
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Application\UseCase\DeliverNews;

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $deliverNews = $container->get(DeliverNews::class);
    $deliverNews->execute();
} catch (\Throwable $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}
