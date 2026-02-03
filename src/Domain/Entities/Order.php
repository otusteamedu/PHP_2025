<?php

namespace App\Domain\Entities;

use App\Domain\Enums\OrderStatus;

class Order
{
    private string $id;
    /** @var array<Product> */
    private array $products = [];
    private OrderStatus $status;
    private \DateTime $createdAt;
    private ?string $customerEmail = null;

    public function __construct()
    {
        $this->id = uniqid('order_', true);
        $this->status = OrderStatus::PENDING;
        $this->createdAt = new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
    }

    public function getTotalPrice(): float
    {
        return array_reduce(
            $this->products,
            fn(float $total, Product $product) => $total + $product->getPrice(),
            0.0
        );
    }

    public function setCustomerEmail(?string $email): void
    {
        $this->customerEmail = $email;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }
}