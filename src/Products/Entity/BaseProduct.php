<?php declare(strict_types=1);

namespace Fastfood\Products\Entity;

abstract class BaseProduct implements ProductInterface
{
    protected string $name;
    protected float $price;
    protected array $qualityInfo = [];

    public function setBase(string $name, float $price): void
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function getCost(): float
    {
        return $this->price;
    }

    public function getDescription(): string
    {
        return $this->name;
    }

    public function getQualityInfo(): array
    {
        return $this->qualityInfo;
    }
}