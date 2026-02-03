<?php

namespace App\Infrastructure\Templates;

use App\Domain\Entities\Product;

class SandwichCookingTemplate extends AbstractCookingTemplate
{
    protected function prepare(Product $product): void
    {
        echo "Поджариваем хлеб...\n";
        echo "Добавляем сыр и начинку...\n";
        sleep(1);
    }
}