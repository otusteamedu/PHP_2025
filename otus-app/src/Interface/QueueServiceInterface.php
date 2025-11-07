<?php

namespace App\Interface;

use App\Enum\QueueNameEnum;

interface QueueServiceInterface
{
    public function publish(QueueNameEnum $queueName, string $message): void;
    public function consume(QueueNameEnum $queueName, callable $callback): void;
}
