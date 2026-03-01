#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\MessageQueue\RabbitMQConnection;
use App\Infrastructure\MessageQueue\RabbitMQConsumer;
use App\Infrastructure\Services\FakeBankReportGenerator;
use App\Infrastructure\Services\SmtpEmailSender;
use App\Application\Workers\ReportWorker;

$connection = new RabbitMQConnection(
    host: getenv('RABBITMQ_HOST') ?: 'rabbitmq',
    port: (int)(getenv('RABBITMQ_PORT') ?: 5672),
    user: getenv('RABBITMQ_USER') ?: 'guest',
    password: getenv('RABBITMQ_PASSWORD') ?: 'guest'
);

$consumer = new RabbitMQConsumer($connection);

$reportGenerator = new FakeBankReportGenerator();

$emailSender = new SmtpEmailSender(
    host: getenv('SMTP_HOST') ?: 'mailhog',
    port: (int)(getenv('SMTP_PORT') ?: 1025)
);

$worker = new ReportWorker($consumer, $reportGenerator, $emailSender);

try {
    $worker->run();
} catch (\Throwable $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
