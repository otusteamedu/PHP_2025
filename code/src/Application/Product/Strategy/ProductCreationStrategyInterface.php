<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Strategy;

use Otus\Code\Domain\Product\Contract\ProductInterface;

interface ProductCreationStrategyInterface
{
    public function create(): ProductInterface;
}
