<?php

declare(strict_types=1);

namespace App\Domain\Product\Factory;

use App\Domain\Interfaces\ProductInterface;
use App\Domain\Interfaces\ProductStrategyInterface;
use InvalidArgumentException;

class ProductFactory
{
    private array $strategies = [];

    public function __construct(array $strategies = [])
    {
        foreach ($strategies as $strategy) {
            $this->addStrategy($strategy);
        }
    }

    public function addStrategy(ProductStrategyInterface $strategy): void
    {
        $this->strategies[$strategy->getType()] = $strategy;
    }

    public function createProduct(string $type): ProductInterface
    {
        if (!isset($this->strategies[$type])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Неизвестный тип продукта: %s. Доступные типы: %s',
                    $type,
                    implode(', ', array_keys($this->strategies))
                )
            );
        }

        return $this->strategies[$type]->createProduct();
    }

    public function getAvailableTypes(): array
    {
        return array_keys($this->strategies);
    }
}
