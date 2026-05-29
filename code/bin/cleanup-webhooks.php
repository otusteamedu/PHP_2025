<?php

declare(strict_types=1);

/**
 * Скрипт очистки processed_webhooks старше 7 дней
 * Запуск: docker exec app php bin/cleanup-webhooks.php
 * Или по cron: 0 3 * * * php /data/bin/cleanup-webhooks.php
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use Psr\Log\LoggerInterface;

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $db = $container->get(DatabaseConnectionInterface::class);
    $logger = $container->get(LoggerInterface::class);

    $pdo = $db->getConnection();
    $stmt = $pdo->query('SELECT cleanup_processed_webhooks(7)');
    $deletedCount = $stmt->fetchColumn();

    $logger->info("Очистка processed_webhooks: удалено записей — {$deletedCount}");
    echo "Удалено записей старше 7 дней: {$deletedCount}\n";
} catch (\Throwable $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}
