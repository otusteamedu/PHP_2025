<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Product\ProductInterface;

class OrderBuilder
{
    /** @var OrderItem[] */
    private array $items = [];

    public function addProduct(ProductInterface $product, int $quantity): self
    {
        $this->items[] = new OrderItem($product, $quantity);

        return $this;
    }

    public function build(): Order
    {
        $order = new Order();

        foreach ($this->items as $item) {
            $order->addItem($item);
        }

        return $order;
    }
}
