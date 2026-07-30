<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Repository\TaskRepositoryInterface;
use App\Infrastructure\Queue\Config\RabbitMqConfigLoader;
use App\Infrastructure\Queue\RabbitMqTaskQueue;

class RabbitMqTaskQueueFactory
{
    /**
     * @throws \Exception
     */
    public function create(TaskRepositoryInterface $taskRepository): RabbitMqTaskQueue
    {
        return new RabbitMqTaskQueue((new RabbitMqConfigLoader())::load(), $taskRepository);
    }
}
