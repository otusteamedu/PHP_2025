<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Channel\Sms;

use App\Domain\OrderNotification\Channel\NotificationHandlerInterface;
use App\Domain\OrderNotification\Contract\NotificationSmsSenderInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\ValueObject\Recipient\Phone;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

class SmsNotificationHandler implements NotificationHandlerInterface
{
    public function __construct(
        private readonly SmsMessageBuilder $builder,
        private readonly NotificationSmsSenderInterface $sender,
    ) {
    }

    public function supports(NotificationChannel $channel): bool
    {
        return $channel === NotificationChannel::Sms;
    }

    public function handle(NotificationTask $task): void
    {
        $message = $this->builder->build($task);

        /** @var Phone $to */
        $to = $task->getRecipient();

        $this->sender->send($message, $to);
    }
}
