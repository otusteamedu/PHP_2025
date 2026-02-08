<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Observer;

use Ak\Hw\Domain\Notification\NotificationObserverInterface;

class Order implements OrderObservableInterface
{
    private array $observers = [];
    private string $status;

    public function __construct(private int $id)
    {
    }

    public function addObserver(NotificationObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function removeObserver(NotificationObserverInterface $observer): void
    {
        // Implementation to remove observer
    }

    public function notifyObservers(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->notifyObservers();
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
}
