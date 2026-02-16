<?php

namespace App\Application\Observer;

use App\Domain\Entity\Interface\ProductInterface;

class CookingStatusSubject
{
    /** @var array<int, CookingObserverInterface> */
    private array $observers = [];

    public function attach(CookingObserverInterface $observer): void
    {
        $this->observers[spl_object_id($observer)] = $observer;
    }

    public function detach(CookingObserverInterface $observer): void
    {
        unset($this->observers[spl_object_id($observer)]);
    }

    public function changeStatus(ProductInterface $product, string $newStatus): void
    {
        $oldStatus = $product->getCookingStatus();
        if ($oldStatus === $newStatus) {
            return;
        }

        $product->setCookingStatus($newStatus);

        $event = new CookingStatusChangedEvent(
            productName: $product->getName(),
            oldStatus: $oldStatus,
            newStatus: $newStatus
        );

        foreach ($this->observers as $observer) {
            $observer->update($event);
        }
    }
}
