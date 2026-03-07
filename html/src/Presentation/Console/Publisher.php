<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Console;

use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Infrastructure\Queue\Payload;
use Otus\Queue\Infrastructure\Queue\QueueInterface;

final readonly class Publisher
{
    /**
     * @param QueueInterface $queue
     */
    public function __construct(
        private QueueInterface $queue,
    ) {
    }

    /**
     * @param string $text
     *
     * @return int
     */
    public function __invoke(string $text): int
    {
        $this
            ->queue
            ->push(
                'realtime',
                new Payload([
                    'queue' => '',
                    'exchange' => 'chat',
                    'message' => json_encode(
                        new Message('System', $text, time())->toArray()
                    ),
                ])
            );

        return 0;
    }
}
