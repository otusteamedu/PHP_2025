<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Entities\Order;
use App\Domain\Interfaces\OrderBuilderInterface;
use App\Domain\Interfaces\ProductInterface;

class OrderBuilder implements OrderBuilderInterface
{
    private ?Order $order = null;

    public function create(): self
    {
        $this->order = new Order($this->generateOrderId());
        return $this;
    }

    public function addProduct(ProductInterface $product): self
    {
        if ($this->order === null) {
            $this->create();
        }

        $this->order->addItem($product);
        return $this;
    }

    public function build(): Order
    {
        if ($this->order === null) {
            $this->create();
        }

        $order = $this->order;
        $this->reset();

        return $order;
    }

    public function reset(): self
    {
        $this->order = null;
        return $this;
    }

    private function generateOrderId(): string
    {
        return 'ORD-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
    }
}
