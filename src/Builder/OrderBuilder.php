<?php

namespace Hafiz\Php2025\Builder;
use Hafiz\Php2025\Product\Product;

class OrderBuilder {
    private Order $order;

    public function __construct() {
        $this->order = new Order();
    }

    public function addProduct(Product $product): self {
        $this->order->addProduct($product);
        return $this;
    }

    public function build(): Order {
        return $this->order;
    }
}
