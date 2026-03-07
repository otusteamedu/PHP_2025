<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Console;

use Otus\Queue\Infrastructure\Queue\Payload;
use Otus\Queue\Infrastructure\Queue\QueueInterface;

final readonly class Consumer
{
    /**
     * @param QueueInterface $queue
     */
    public function __construct(
        private QueueInterface $queue,
    ) {
    }

    /**
     * @return int
     */
    public function __invoke(): int
    {
        $this
            ->queue
            ->pull(
                'realtime',
                new Payload([
                    'queue' => '',
                    'exchange' => 'chat',
                ])
            );

        return 0;
    }
}
