<?php

use App\Service\RabbitMQService;
use App\Service\QueueServiceInterface;

return [
    QueueServiceInterface::class => function () {
        return new RabbitMQService();
    }
];