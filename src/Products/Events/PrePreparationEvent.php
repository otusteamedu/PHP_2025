<?php declare(strict_types=1);

namespace Fastfood\Products\Events;

use Fastfood\Products\Entity\ProductInterface;

class PrePreparationEvent implements PreparationEventInterface
{
    private ProductInterface $product;
    private string $eventType;
    private int $timestamp;

    public function __construct(ProductInterface $product, string $eventType = 'pre_preparation')
    {
        $this->product = $product;
        $this->eventType = $eventType;
        $this->timestamp = time();
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
}