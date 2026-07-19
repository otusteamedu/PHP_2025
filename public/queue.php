<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Infrastructure\Controller\QueueController;
use App\Infrastructure\Factory\RabbitMqTaskQueueFactory;

$queue = (new RabbitMqTaskQueueFactory())->create();

$controller = new QueueController($queue);

$controller->handle();