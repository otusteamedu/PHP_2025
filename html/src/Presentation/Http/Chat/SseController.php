<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http\Chat;

use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;
use Otus\Queue\Infrastructure\Queue\Payload;
use Otus\Queue\Infrastructure\Queue\QueueInterface;

final readonly class SseController
{
    /**
     * @param QueueInterface $queue
     */
    public function __construct(
        private QueueInterface $queue,
    ) {
    }

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface
    {
        $queue = $this->queue;

        return ResponseFactory::toStream(
            callback: static function () use ($queue): void {
                $queue->pull('realtime', new Payload([
                    'queue' => '',
                    'exchange' => 'chat',
                ]));
            },
            headers: [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                /**
                 * nginx.
                 */
                'X-Accel-Buffering' => 'no',
                'X-Accel-Expires' => '0',
            ]
        );
    }
}
