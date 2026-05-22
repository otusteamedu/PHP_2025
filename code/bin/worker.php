<?php

declare(strict_types=1);

/**
 * Консольный worker для обработки запросов из очереди RabbitMQ
 */

use App\Database\DBConnectionFactory;
use App\Queue\QueueConsumerFactory;
use App\Repository\RequestRepository;
use App\Service\RequestMessageHandler;

require dirname(__DIR__) . '/vendor/autoload.php';

$repository = new RequestRepository((new DBConnectionFactory())->create());
$messageHandler = new RequestMessageHandler($repository);

$consumer = (new QueueConsumerFactory())->create();
$consumer->consume([$messageHandler, 'handle']);
