<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Strategy;

use Otus\Code\Domain\Product\Contract\ProductInterface;
use Otus\Code\Domain\Product\Entity\HotDog;

final class HotDogStrategy implements ProductCreationStrategyInterface
{
    public function create(): ProductInterface
    {
        return new HotDog();
    }
}
