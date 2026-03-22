<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Recipe;

use Otus\Code\Domain\Product\Contract\ProductInterface;

final class ProductCustomizer
{
    public function applyRecipe(ProductInterface $product, Recipe $recipe): ProductInterface
    {
        return $this->applyDecorators($product, $recipe->getDecorators());
    }

    /**
     * @param class-string[] $decorators
     */
    public function applyCustomIngredients(ProductInterface $product, array $decorators): ProductInterface
    {
        return $this->applyDecorators($product, $decorators);
    }

    /**
     * @param class-string[] $decorators
     */
    private function applyDecorators(ProductInterface $product, array $decorators): ProductInterface
    {
        foreach ($decorators as $decoratorClass) {
            $product = new $decoratorClass($product);
        }

        return $product;
    }
}
