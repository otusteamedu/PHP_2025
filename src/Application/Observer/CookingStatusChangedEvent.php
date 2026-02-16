<?php

namespace App\Application\Observer;

class CookingStatusChangedEvent
{
    public readonly string $productName;

    public readonly string $oldStatus;

    public readonly string $newStatus;

    public function __construct(
        string $productName,
        string $oldStatus,
        string $newStatus,
    ) {
        $this->productName = $productName;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
