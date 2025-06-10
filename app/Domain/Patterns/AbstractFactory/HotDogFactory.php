<?php
declare(strict_types=1);

namespace App\Domain\Patterns\AbstractFactory;

use App\Domain\Entity\Products\BasicProduct\OriginalHotDog;
use App\Domain\Entity\Products\BasicProduct\SpicyHotDog;

class HotDogFactory implements ProductFactoryInterface
{

    public function createOriginal(): OriginalHotDog
    {
        return new OriginalHotDog();
    }

    public function createSpicy(): SpicyHotDog
    {
        return new SpicyHotDog();
    }
}