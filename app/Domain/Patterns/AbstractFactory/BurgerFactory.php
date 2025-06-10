<?php
declare(strict_types=1);

namespace App\Domain\Patterns\AbstractFactory;

use App\Domain\Entity\Products\BasicProduct\OriginalBurger;
use App\Domain\Entity\Products\BasicProduct\SpicyBurger;

class BurgerFactory implements ProductFactoryInterface
{

    public function createOriginal(): OriginalBurger
    {
        return new OriginalBurger();
    }

    public function createSpicy(): SpicyBurger
    {
        return new SpicyBurger();
    }
}