<?php

declare(strict_types=1);

/**
 * Точка входа consumer Telegram Long Polling
 * Запускается через supervisor
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Infrastructure\TelegramBot\TelegramLongPollWorker;

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $worker = $container->get(TelegramLongPollWorker::class);
    $worker->run();
} catch (\Throwable $e) {
    echo "Фатальная ошибка consumer-telegram-longpoll: " . $e->getMessage() . "\n";
    exit(1);
}
