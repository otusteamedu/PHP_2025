<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Создаем и вызываем команду
try {
    $container = new DI\Container();
    $dependencies = require __DIR__ . '/../dependencies.php';
    $dependencies($container);
    $command =  $container->get(\App\Presentation\Console\Command\GenerateTrainingNotificationsCommand::class);
    $command();
} catch (\Throwable $e) {
    // Логируем ошибку, чтобы cron не "молчал" в случае сбоя
    error_log('Error generating training notifications: ' . $e->getMessage());
    exit(1); // Выходим с кодом ошибки
}

exit(0); // Успешное завершение