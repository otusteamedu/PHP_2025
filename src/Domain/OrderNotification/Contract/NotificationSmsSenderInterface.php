<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Contract;

use App\Domain\OrderNotification\Exception\Delivery\NotificationSendingException;
use App\Domain\OrderNotification\ValueObject\Message\SmsMessage;
use App\Domain\OrderNotification\ValueObject\Recipient\Phone;

interface NotificationSmsSenderInterface
{
    /**
     * @throws NotificationSendingException
     */
    public function send(SmsMessage $message, Phone $phone): void;
}
