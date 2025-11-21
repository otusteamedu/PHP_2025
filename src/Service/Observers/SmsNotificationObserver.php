<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Observers;

use Dinargab\Homework15\Model\Observer\OrderObserverInterface;
use Dinargab\Homework15\Model\Order\DTO\ProductOrderNotificationDTO;


class SmsNotificationObserver implements OrderObserverInterface
{
    public function __construct(private NotificationMessageFactory $notificationMessageFactory)
    {

    }
    public function update(ProductOrderNotificationDTO $notificationDTO): void
    {
        echo "SMS Notification: " . $this->notificationMessageFactory->createMessage($notificationDTO) . "</br>";
    }
}