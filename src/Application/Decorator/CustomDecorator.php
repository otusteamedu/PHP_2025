<?php

namespace App\Application\Decorator;

use App\Domain\Entity\Interface\ProductInterface;

class CustomDecorator extends ProductDecorator
{
    private array $ingredients;

    public function __construct(ProductInterface $product, array $ingredients)
    {
        parent::__construct($product);
        $this->ingredients = $ingredients;
    }

    public function getName(): string
    {
        $baseName = $this->product->getName();

        if ($this->ingredients === []) {
            return $baseName;
        }

        return str_starts_with($baseName, 'Custom ') ? $baseName : 'Custom ' . $baseName;
    }

    protected function additionalIngredients(): array
    {
        return $this->ingredients;
    }
}
