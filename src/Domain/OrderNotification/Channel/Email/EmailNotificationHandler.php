<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Channel\Email;

use App\Domain\OrderNotification\Channel\NotificationHandlerInterface;
use App\Domain\OrderNotification\Contract\NotificationMailerInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\ValueObject\Recipient\Email;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

class EmailNotificationHandler implements NotificationHandlerInterface
{
    public function __construct(
        private readonly EmailMessageBuilder $builder,
        private readonly NotificationMailerInterface $mailer,
    ) {
    }

    public function supports(NotificationChannel $channel): bool
    {
        return $channel === NotificationChannel::Email;
    }

    public function handle(NotificationTask $task): void
    {
        $message = $this->builder->build($task);

        /** @var Email $to */
        $to = $task->getRecipient();

        $this->mailer->send($message, $to);
    }
}
