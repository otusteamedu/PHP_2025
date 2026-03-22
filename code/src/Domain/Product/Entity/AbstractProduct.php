<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Product\Entity;

use Otus\Code\Domain\Product\Contract\ProductInterface;

abstract class AbstractProduct implements ProductInterface
{
    /**
     * @param string[] $ingredients
     */
    public function __construct(
        private readonly string $name,
        private readonly array $ingredients,
        private readonly int $basePrice,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getBasePrice(): int
    {
        return $this->basePrice;
    }

    public function getPrice(): int
    {
        return $this->basePrice;
    }

    public function describe(): string
    {
        return $this->name . ' (' . implode(', ', $this->ingredients) . ') - ' . $this->getPrice();
    }
}
