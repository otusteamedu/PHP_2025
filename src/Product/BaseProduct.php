<?php

declare(strict_types=1);

namespace Restaurant\Product;

abstract class BaseProduct implements ProductInterface
{
    protected string $description;
    protected float $price;

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function cook(): void
    {
        echo "Готовим: {$this->getDescription()}\n";
    }

    public function isQualityAcceptable(): bool
    {
        return (rand(1, 10) <= 9);
    }
}
