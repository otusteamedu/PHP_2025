<?php

declare(strict_types=1);

/**
 * Точка входа consumer дублирования Max->Telegram
 * Запускается через supervisor
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Infrastructure\Queue\TelegramForwardConsumer;

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $consumer = $container->get(TelegramForwardConsumer::class);
    $consumer->consume();
} catch (\Throwable $e) {
    echo "Фатальная ошибка consumer-telegram-forward: " . $e->getMessage() . "\n";
    exit(1);
}
