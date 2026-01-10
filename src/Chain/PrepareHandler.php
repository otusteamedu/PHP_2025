<?php declare(strict_types=1);

namespace App\Chain;

use App\Core\FoodProductInterface;

class PrepareHandler extends AbstractHandler
{
    public function handle(FoodProductInterface $product): void
    {
        echo "Подготовка к приготовлению: " . $product->getName() . PHP_EOL;
        parent::handle($product);
    }
}
