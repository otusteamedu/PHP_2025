<?php

namespace Blarkinov\Hw1500\Infrastructure\Gateway\Sender;

use Blarkinov\Hw1500\Domain\Observer\OrderStatusEvent;
use Blarkinov\Hw1500\Domain\Observer\SubscriberOrderStatusInterface;

class MailSender implements SubscriberOrderStatusInterface
{
    public function update(OrderStatusEvent $event): void
    {
        echo basename(self::class).":\n";
        echo "id - ".$event->getId()."\n";
        echo "status - ".$event->getStatus()."\n";
    }
}
