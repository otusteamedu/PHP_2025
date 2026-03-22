<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Strategy;

use Exception;
use Otus\Code\Domain\Product\Contract\ProductInterface;

final class DefaultProductCreationStrategy implements ProductCreationStrategyInterface
{
    public function create(): ProductInterface
    {
        throw new Exception('Strategy must be selected before creating a product.');
    }
}
