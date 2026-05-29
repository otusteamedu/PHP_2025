<?php

declare(strict_types=1);

/**
 * Скрипт декларации топологии RabbitMQ
 * Запуск: php bin/setup-rabbitmq.php
 * Или через make setup
 */

require __DIR__ . '/../vendor/autoload.php';

// Загрузка .env — единая точка (config/bootstrap.php)
require __DIR__ . '/../config/bootstrap.php';

try {
    $connection = new \AMQPConnection([
        'host' => $_ENV['RABBITMQ_HOST'] ?? 'rabbitmq',
        'port' => (int)($_ENV['RABBITMQ_PORT'] ?? 5672),
        'login' => $_ENV['RABBITMQ_LOGIN'] ?? 'guest',
        'password' => $_ENV['RABBITMQ_PASSWORD'] ?? 'guest',
        'vhost' => $_ENV['RABBITMQ_VHOST'] ?? '/',
    ]);
    $connection->connect();

    $channel = new \AMQPChannel($connection);

    // === Экcчейнджи ===

    // Основной экcчейндж (direct)
    $directExchange = new \AMQPExchange($channel);
    $directExchange->setName('mkd.direct');
    $directExchange->setType(AMQP_EX_TYPE_DIRECT);
    $directExchange->setFlags(AMQP_DURABLE);
    $directExchange->declareExchange();
    echo "Exchange создан: mkd.direct\n";

    // DLX-экcчейндж (direct)
    $dlxExchange = new \AMQPExchange($channel);
    $dlxExchange->setName('mkd.dlx');
    $dlxExchange->setType(AMQP_EX_TYPE_DIRECT);
    $dlxExchange->setFlags(AMQP_DURABLE);
    $dlxExchange->declareExchange();
    echo "Exchange создан: mkd.dlx\n";

    // === Основные очереди (с DLX) ===

    $queues = [
        'mkd.telegram.forward',
        'mkd.rag.query',
        'mkd.news.delivery',
    ];

    foreach ($queues as $queueName) {
        $queue = new \AMQPQueue($channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setArguments([
            'x-dead-letter-exchange' => 'mkd.dlx',
            'x-dead-letter-routing-key' => $queueName,
        ]);
        $queue->declareQueue();
        echo "Очередь создана: {$queueName}\n";

        // Привязка: mkd.direct -> очередь (routing key = имя очереди)
        $queue->bind('mkd.direct', $queueName);
        echo "Binding: mkd.direct -> {$queueName} (routing key: {$queueName})\n";
    }

    // === Retry-очереди (с TTL 60с) ===

    $retryQueues = [
        'retry.telegram.forward' => 'mkd.telegram.forward',
        'retry.rag.query' => 'mkd.rag.query',
        'retry.news.delivery' => 'mkd.news.delivery',
    ];

    foreach ($retryQueues as $retryName => $originalName) {
        $queue = new \AMQPQueue($channel);
        $queue->setName($retryName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setArguments([
            'x-message-ttl' => 60000,
            'x-dead-letter-exchange' => 'mkd.direct',
            'x-dead-letter-routing-key' => $originalName,
        ]);
        $queue->declareQueue();
        echo "Retry-очередь создана: {$retryName} -> {$originalName}\n";

        // Привязка: mkd.dlx -> retry-очередь (routing key = имя исходной очереди)
        $queue->bind('mkd.dlx', $originalName);
        echo "Binding: mkd.dlx -> {$retryName} (routing key: {$originalName})\n";
    }

    // === DLQ (Dead Letter Queue) ===

    $fallbackQueueName = $_ENV['RABBITMQ_QUEUE_FALLBACK'] ?? 'mkd.fallback';

    $dlq = new \AMQPQueue($channel);
    $dlq->setName($fallbackQueueName);
    $dlq->setFlags(AMQP_DURABLE);
    $dlq->declareQueue();
    echo "DLQ создана: {$fallbackQueueName}\n";

    // Привязка: mkd.dlx -> fallback-очередь (routing key = имя fallback-очереди)
    $dlq->bind('mkd.dlx', $fallbackQueueName);
    echo "Binding: mkd.dlx -> {$fallbackQueueName} (routing key: {$fallbackQueueName})\n";

    $connection->disconnect();

    echo "\n+++ Топология RabbitMQ успешно создана!\n";
} catch (\AMQPException $e) {
    echo "--- Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}
