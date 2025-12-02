<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Queue;

use Dinargab\Homework20\Domain\Job\Entity\Job;

interface QueueServiceInterface
{
    public function push(Job $job): void;
    public function pull(callable $callback): \Generator;
}