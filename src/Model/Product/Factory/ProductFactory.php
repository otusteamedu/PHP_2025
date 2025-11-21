<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product\Factory;

use Dinargab\Homework15\Builder\Director\RecipesDirector;
use Dinargab\Homework15\Builder\Factory\BuilderFactory;
use Dinargab\Homework15\Model\Product\Decorators\Selector\ProductDecoratorSelector;
use Dinargab\Homework15\Model\Product\ProductInterface;

class ProductFactory
{

    private bool $premium = false;

    public function __construct(
        private RecipesDirector          $recipesDirector,
        private BuilderFactory           $builderFactory,
        private ProductDecoratorSelector $productDecoratorSelector
    )
    {

    }

    public function create(string $type, array $additionalIngredients): ProductInterface
    {
        if (str_contains($type, "premium")) {
            $this->premium = true;
            $type = trim(str_replace("premium", "", $type));
        }

        $builder = $this->builderFactory->getBuilder($type);

        $this->recipesDirector->setBuilder($builder);


        if ($this->premium) {
            $product = $this->recipesDirector->preparePremiumProduct();
        } else {
            $product = $this->recipesDirector->prepareNormalProduct();
        }
        if (!empty($additionalIngredients)) {
            foreach ($additionalIngredients as $additionalIngredient) {
                $product = $this->productDecoratorSelector->getDecoratedProduct($additionalIngredient, $product);
            }
        }
        return $product;

    }
}