<?php

declare(strict_types=1);

namespace Otus\Code\Infrastructure\Notification\Observer;

use Otus\Code\Domain\Order\Entity\Order;
use Otus\Code\Domain\Order\Event\OrderEvent;
use Otus\Code\Domain\Order\Observer\OrderObserverInterface;
use Otus\Code\Infrastructure\Notification\Channel\NotificationChannelInterface;

abstract class AbstractOrderNotificationObserver implements OrderObserverInterface
{
    public function __construct(
        private readonly string $recipient,
        private readonly NotificationChannelInterface $channel,
    ) {
    }

    public function update(Order $order, OrderEvent $event): void
    {
        $this->channel->send(
            $this->recipient,
            $this->messagePrefix()
            . ': order: ' . $order->getId()
            . ', event: ' . $event->getDescription()
            . ', status: ' . $order->getStatus()->label()
            . '.',
        );
    }

    abstract protected function messagePrefix(): string;
}
