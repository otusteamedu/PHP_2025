<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Order\Entity;

use Otus\Code\Domain\Order\Enum\OrderStatus;
use Otus\Code\Domain\Order\Event\OrderEvent;
use Otus\Code\Domain\Order\Observer\OrderObserverInterface;
use Otus\Code\Domain\Product\Contract\ProductInterface;

final class Order
{
    /**
     * @var ProductInterface[]
     */
    private array $items = [];

    /**
     * @var OrderObserverInterface[]
     */
    private array $observers = [];

    /**
     * @var OrderEvent[]
     */
    private array $events = [];

    public function __construct(
        private readonly string $id,
        private OrderStatus $status = OrderStatus::Draft,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @return ProductInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return OrderEvent[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function attach(OrderObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function addItem(ProductInterface $item): void
    {
        $this->items[] = $item;
        $this->recordEvent(new OrderEvent('item_added', $item->getName() . ' added to order ' . $this->id, $this->status));
    }

    public function changeStatus(OrderStatus $status): void
    {
        $this->status = $status;
        $this->recordEvent(new OrderEvent('status_changed', 'Order ' . $this->id . ' moved to ' . $status->label(), $status));
    }

    public function getTotalPrice(): int
    {
        return array_reduce(
            $this->items,
            static fn (int $total, ProductInterface $item): int => $total + $item->getPrice(),
            0,
        );
    }

    private function recordEvent(OrderEvent $event): void
    {
        $this->events[] = $event;

        foreach ($this->observers as $observer) {
            $observer->update($this, $event);
        }
    }
}
