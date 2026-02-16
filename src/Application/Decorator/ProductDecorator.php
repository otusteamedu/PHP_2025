<?php

namespace App\Application\Decorator;

use App\Application\Decorator\Ingredient\BreadDecorator;
use App\Application\Decorator\Ingredient\BunDecorator;
use App\Domain\Entity\Interface\ProductInterface;

abstract class ProductDecorator implements ProductInterface
{
    protected ProductInterface $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getName(): string
    {
        return $this->product->getName();
    }

    public function getIngredients(): array
    {
        return array_merge(
            $this->product->getIngredients(),
            $this->additionalIngredients()
        );
    }

    public function getCookingStatus(): string
    {
        return $this->product->getCookingStatus();
    }

    public function setCookingStatus(string $status): void
    {
        $this->product->setCookingStatus($status);
    }

    public function isMeetsStandard(): bool
    {
        $ingredients = $this->getIngredients();

        $isBunIncluded = in_array(BunDecorator::BUN, $ingredients, true);
        $isBreadIncluded = in_array(BreadDecorator::BREAD, $ingredients, true);
        $isEqualsMinQuantity = count($ingredients) >= 2;

        return ($isBunIncluded || $isBreadIncluded) && $isEqualsMinQuantity;
    }

    /** @return string[] */
    abstract protected function additionalIngredients(): array;
}
