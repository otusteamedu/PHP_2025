<?php

namespace App\Domain\Entity;

use App\Infrastructure\Builder\ProductBuilder;

class Product
{
    private string $type;
    private string $status;
    private array $ingredients;

    public function __construct(ProductBuilder $builder) {
        $this->type = $builder->getType();
        $this->status = $builder->getStatus();
        $this->ingredients = $builder->getIngredients();
    }

    public function getType(): string {
        return $this->type;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getIngredients(): array {
        return $this->ingredients;
    }
}