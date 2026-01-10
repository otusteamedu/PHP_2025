<?php declare(strict_types=1);

namespace App\Proxy;

use App\Core\FoodProductInterface;

class CookingProxy
{
    public function process(FoodProductInterface $product): void
    {
        echo "Проверка состава" . PHP_EOL;

        if (count($product->getIngredients()) < 3) {
            echo "Не прошёл проверку и утилизирован" . PHP_EOL;
        } else {
            echo "Состав ок, можно готовить" . PHP_EOL;
        }

        echo $product->getDescription() . PHP_EOL;
    }
}
