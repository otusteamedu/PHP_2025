<?php

declare(strict_types=1);

namespace Restaurant\Builder;

use Restaurant\Product\ProductInterface;

interface BuilderInterface
{
    public function reset(): void;

    public function getProduct(): ProductInterface;
}
