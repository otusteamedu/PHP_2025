<?php

namespace App\Application\Service;

use App\Application\Decorator\CustomDecorator;
use App\Application\Decorator\RecipeDecorator;
use App\Domain\Entity\Interface\ProductInterface;
use App\Domain\Entity\Interface\RecipeAwareInterface;

class ProductAssembler
{
    /**
     * @param ProductInterface $baseProduct
     * @param string[] $optional
     * @return ProductInterface
     */
    public function assemble(ProductInterface $baseProduct, array $optional): ProductInterface
    {
        if ($baseProduct instanceof RecipeAwareInterface && $optional === []) {
            $baseProduct = new RecipeDecorator($baseProduct, $baseProduct->getRecipe());
        } else {
            $baseProduct = new CustomDecorator($baseProduct, $optional);
        }

        return $baseProduct;
    }
}
