<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\Order;

interface OrderBuilderInterface
{
    public function createOrder(): self;
    public function addProduct(string $productType, array $ingredients = []): self;
    public function setCustomerEmail(string $email): self;
    public function getOrder(): Order;
}