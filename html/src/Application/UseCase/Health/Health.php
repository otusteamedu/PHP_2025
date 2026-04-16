<?php

declare(strict_types=1);

namespace Otus\Queue\Application\UseCase\Health;

use Otus\Queue\Infrastructure\Database\DatabaseInterface;
use Otus\Queue\Infrastructure\Queue\QueueInterface;

final readonly class Health
{
    /**
     * @param DatabaseInterface $database
     * @param QueueInterface $queue
     */
    public function __construct(
        private DatabaseInterface $database,
        private QueueInterface $queue,
    ) {
    }

    /**
     * @return bool
     */
    public function handle(): bool
    {
        return $this->database->ping() && $this->queue->ping();
    }
}
