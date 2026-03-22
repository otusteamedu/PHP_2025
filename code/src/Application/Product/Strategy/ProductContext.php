<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Strategy;

use Otus\Code\Domain\Product\Contract\ProductInterface;

final class ProductContext
{
    public function __construct(
        private ProductCreationStrategyInterface $strategy,
    ) {
    }

    public function useStrategy(ProductCreationStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function createBaseProduct(): ProductInterface
    {
        return $this->strategy->create();
    }
}
