<?php
namespace App\Builder;

use App\Order\Order;
use App\Order\OrderInterface;
use App\Products\ProductInterface;

class OrderBuilder
{
    private Order $order;

    public function reset(): void
    {
        $this->order = new Order();
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->order->setProduct($product);
    }

    public function setCustomerName(string $name): void
    {
        $this->order->setCustomerName($name);
    }

    public function setDeliveryAddress(string $address): void
    {
        $this->order->setDeliveryAddress($address);
    }

    public function getOrder(): OrderInterface
    {
        $result = $this->order;
        $this->reset();
        return $result;
    }
}
