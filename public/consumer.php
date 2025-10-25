<?php

use Application\Event\EventConsumeUseCase;
use Application\Queue\RabbitMQ;

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

try {
    (new EventConsumeUseCase(new RabbitMQ()))->run();
} catch (AMQPConnectionException $e) {
    echo $e->getMessage() . "\n";
} catch (Throwable $e) {
    echo $e->getMessage() . "\n";
}
