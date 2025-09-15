<?php declare(strict_types=1);

namespace Fastfood\Products\Events;

use Fastfood\Products\Entity\ProductInterface;

class PostPreparationEvent implements PreparationEventInterface
{
    private ProductInterface $product;
    private string $eventType;
    private int $timestamp;
    private bool $passedQualityControl;
    private string $qualityMessage;

    public function __construct(
        ProductInterface $product,
        bool $passedQualityControl,
        string $qualityMessage = '',
        string $eventType = 'post_preparation'
    ) {
        $this->product = $product;
        $this->passedQualityControl = $passedQualityControl;
        $this->qualityMessage = $qualityMessage;
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

    public function passedQualityControl(): bool
    {
        return $this->passedQualityControl;
    }

    public function getQualityMessage(): string
    {
        return $this->qualityMessage;
    }
}