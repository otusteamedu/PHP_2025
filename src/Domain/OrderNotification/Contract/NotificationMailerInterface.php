<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Contract;

use App\Domain\OrderNotification\Exception\Delivery\NotificationSendingException;
use App\Domain\OrderNotification\ValueObject\Message\EmailMessage;
use App\Domain\OrderNotification\ValueObject\Recipient\Email;

interface NotificationMailerInterface
{
    /**
     * @throws NotificationSendingException
     */
    public function send(EmailMessage $message, Email $email): void;
}
