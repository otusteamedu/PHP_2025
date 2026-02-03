<?php

namespace App\Infrastructure\Templates;

use App\Domain\Interfaces\CookingTemplateInterface;
use App\Domain\Entities\Product;
use App\Domain\Enums\ProductStatus;

abstract class AbstractCookingTemplate implements CookingTemplateInterface
{
    final public function cook(Product $product): Product
    {
        $this->preCook($product);
        $this->prepare($product);
        $this->postCook($product);
        
        return $product;
    }

    protected function preCook(Product $product): void
    {
        $product->setStatus(ProductStatus::PREPARING);
        echo "Начинаем готовить: {$product->getName()}\n";
    }

    abstract protected function prepare(Product $product): void;

    protected function postCook(Product $product): void
    {
        if ($this->isValidProduct($product)) {
            $product->setStatus(ProductStatus::READY);
            echo "{$product->getName()} готов!\n";
        } else {
            $product->setStatus(ProductStatus::DISCARDED);
            echo "{$product->getName()} утилизирован - не прошел проверку качества!\n";
        }
    }

    protected function isValidProduct(Product $product): bool
    {
        // Базовая проверка - продукт должен иметь минимум 2 ингредиента
        return count($product->getIngredients()) >= 2;
    }
}
