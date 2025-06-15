<?php

namespace App\Infrastructure\Builder;

use App\Application\Ingredient\IngredientInterface;
use App\Domain\Builder\ProductBuilderInterface;
use App\Domain\Entity\Product;

class ProductBuilder implements ProductBuilderInterface
{
    private readonly string $type;
    private readonly string $status;
    private array $ingredients;

    public function getType(): string {
        return $this->type;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getIngredients(): array {
        $data = [];

        foreach ($this->ingredients as $ingredient) {
            $data[] = $ingredient->add();
        }

        return $data;
    }

    public function setType(string $type): ProductBuilder {
        $this->type = $type;
        return $this;
    }

    public function setStatus(string $status): ProductBuilder {
        $this->status = $status;
        return $this;
    }

    public function addIngredient(IngredientInterface $element): ProductBuilder {
        $this->ingredients[] = $element;
        return $this;
    }

    public function build(): Product {
        return new Product($this);
    }
}