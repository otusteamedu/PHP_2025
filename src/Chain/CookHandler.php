<?php declare(strict_types=1);

namespace App\Chain;

use App\Core\FoodProductInterface;

class CookHandler extends AbstractHandler
{
    public function handle(FoodProductInterface $product): void
    {
        echo "Готовим: " . implode(", ", $product->getIngredients()) . PHP_EOL;
        parent::handle($product);
    }
}
