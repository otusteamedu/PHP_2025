<?php

namespace App\Infrastructure\Templates;

use App\Domain\Entities\Product;

class BurgerCookingTemplate extends AbstractCookingTemplate
{
    protected function prepare(Product $product): void
    {
        echo "Жарим котлету...\n";
        echo "Собираем бургер...\n";
        sleep(1);
    }

    protected function isValidProduct(Product $product): bool
    {
        // Для бургера дополнительная проверка - должна быть котлета
        $ingredients = $product->getIngredients();
        return parent::isValidProduct($product) 
            && in_array('Говяжья котлета', $ingredients);
    }
}

