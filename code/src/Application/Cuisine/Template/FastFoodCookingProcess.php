<?php

declare(strict_types=1);

namespace Otus\Code\Application\Cuisine\Template;

use Otus\Code\Domain\Product\Contract\ProductInterface;

final class FastFoodCookingProcess extends AbstractCookingProcess
{
    protected function cookCore(ProductInterface $product): void
    {
        $this->writeLog('Cooking Fast food: ' . $product->getName());
    }

    protected function meetsStandard(ProductInterface $product): bool
    {
        return count($product->getIngredients()) <= 6;
    }
}
