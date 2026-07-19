<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Application\Handler\TaskConsoleHandler;
use App\Infrastructure\Factory\RabbitMqTaskQueueFactory;

$queue = (new RabbitMqTaskQueueFactory())->create();

echo "Worker started..." . PHP_EOL;

$queue->consume(
    new TaskConsoleHandler()
);