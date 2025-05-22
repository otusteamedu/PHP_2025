<?php
namespace App\Order;

use SplSubject;

interface OrderInterface extends SplSubject
{
    public function getStatus(): string;
    public function setStatus(string $status): void;
    public function setProduct(ProductInterface $product): void;
    public function setCustomerName(string $name): void;
    public function setDeliveryAddress(string $address): void;
}
