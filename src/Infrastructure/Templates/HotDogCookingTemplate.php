<?php

namespace App\Infrastructure\Templates;

use App\Domain\Entities\Product;

class HotDogCookingTemplate extends AbstractCookingTemplate
{
    protected function prepare(Product $product): void
    {
        echo "Разогреваем сосиску...\n";
        echo "Укладываем в булочку...\n";
        sleep(1);
    }
}