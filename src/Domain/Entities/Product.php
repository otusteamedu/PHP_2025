<?php

namespace App\Domain\Entities;

use App\Domain\Enums\ProductType;
use App\Domain\Enums\ProductStatus;

abstract class Product
{
    protected string $id;
    protected string $name;
    protected ProductType $type;
    /** @var array<string> */
    protected array $ingredients = [];
    protected ProductStatus $status;
    protected float $basePrice;

    public function __construct(string $name, ProductType $type, float $basePrice)
    {
        $this->id = uniqid('product_', true);
        $this->name = $name;
        $this->type = $type;
        $this->basePrice = $basePrice;
        $this->status = ProductStatus::CREATED;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ProductType
    {
        return $this->type;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function addIngredient(string $ingredient): void
    {
        $this->ingredients[] = $ingredient;
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    public function setStatus(ProductStatus $status): void
    {
        $this->status = $status;
    }

    public function getBasePrice(): float
    {
        return $this->basePrice;
    }

    abstract public function getPrice(): float;
}