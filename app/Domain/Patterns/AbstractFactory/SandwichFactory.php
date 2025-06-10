<?php
declare(strict_types=1);

namespace App\Domain\Patterns\AbstractFactory;

use App\Domain\Entity\Products\BasicProduct\OriginalSandwich;
use App\Domain\Entity\Products\BasicProduct\SpicySandwich;

class SandwichFactory implements ProductFactoryInterface
{

    public function createOriginal(): OriginalSandwich
    {
        return new OriginalSandwich();
    }

    public function createSpicy(): SpicySandwich
    {
        return new SpicySandwich();
    }
}