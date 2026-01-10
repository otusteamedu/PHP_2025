<?php declare(strict_types=1);

namespace App\Chain;

use App\Core\FoodProductInterface;

class ServeHandler extends AbstractHandler
{
    public function handle(FoodProductInterface $product): void
    {
        echo "Подача: " . $product->getName() . PHP_EOL;
        parent::handle($product);
    }
}
