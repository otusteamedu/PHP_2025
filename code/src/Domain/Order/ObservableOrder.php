<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\Order;

use Ak\Hw\Domain\Common\Observer\ObservableInterface;
use Ak\Hw\Domain\Common\Observer\ObserverInterface;

class ObservableOrder implements ObservableInterface
{
    private array $observers = [];
    private string $status;

    public function __construct(private int $id)
    {
    }

    public function addObserver(ObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function removeObserver(ObserverInterface $observer): void
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
