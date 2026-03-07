<?php

declare(strict_types=1);

namespace Otus\Queue\Application\UseCase\Chat;

use Otus\Queue\Application\Interface\ChatRepositoryInterface;
use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Domain\Validator\MessageValidator;
use Otus\Queue\Infrastructure\Queue\Payload;
use Otus\Queue\Infrastructure\Queue\QueueInterface;

final readonly class Store
{
    /**
     * @param ChatRepositoryInterface $repository
     * @param QueueInterface $queue
     */
    public function __construct(
        private ChatRepositoryInterface $repository,
        private QueueInterface $queue,
    ) {
    }

    /**
     * @param Message $message
     *
     * @return bool
     */
    public function handle(Message $message): bool
    {
        new MessageValidator($message)->validate();

        if ($this->repository->store($message)) {
            $this
                ->queue
                ->push(
                    'realtime',
                    new Payload([
                        'queue' => '',
                        'exchange' => 'chat',
                        'message' => json_encode($message->toArray()),
                    ])
                );

            return true;
        }

        return false;
    }
}
