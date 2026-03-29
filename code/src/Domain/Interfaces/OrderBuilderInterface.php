<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entities\Order;

interface OrderBuilderInterface
{
    public function create(): self;

    public function addProduct(ProductInterface $product): self;

    public function build(): Order;

    public function reset(): self;
}
