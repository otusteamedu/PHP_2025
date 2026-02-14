<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Interfaces\OrderObserverInterface;
use App\Domain\Interfaces\OrderSubjectInterface;
use App\Domain\Entities\Order;
use SplObjectStorage;

class OrderSubject implements OrderSubjectInterface
{
    private SplObjectStorage $observers;
    private ?Order $order = null;

    public function __construct()
    {
        $this->observers = new SplObjectStorage();
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function attach(OrderObserverInterface $observer): void
    {
        $this->observers->offsetSet($observer, null);
    }

    public function detach(OrderObserverInterface $observer): void
    {
        $this->observers->offsetUnset($observer);
    }

    public function notify(string $event): void
    {
        if ($this->order === null) {
            return;
        }

        foreach ($this->observers as $observer) {
            $observer->update($this->order, $event);
        }
    }
}
