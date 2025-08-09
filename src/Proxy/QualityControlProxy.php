<?php

namespace App\Proxy;

use App\Model\Product;
use RuntimeException;

class QualityControlProxy
{
    public function __construct(private array $qualityStandards)
    {
    }

    public function prepare($product): Product
    {
        $this->preCheck($product);
        return $product;
    }

    private function preCheck(Product $product): void
    {
        echo "Проверка продукта {$product->getName()} на соответствие стандартам качества ...\n";
        if (!$this->meetsQualityStandards($product)) {
            throw new RuntimeException('Качество продукта не соответствует установленным стандартам');
        }

        echo "Проверка качества пройдена\n";
    }

    private function meetsQualityStandards(Product $product): bool
    {
        if (isset($this->qualityStandards['min_ingredients'])) {
            $ingredients = $product->getIngredients();
            $ingredientCount = count($ingredients);
            if ($ingredientCount < $this->qualityStandards['min_ingredients']) {
                return false;
            }
        }

        return true;
    }
}