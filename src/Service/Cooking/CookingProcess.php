<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Cooking;

use Dinargab\Homework15\Model\Product\Factory\ProductFactory;
use Dinargab\Homework15\Model\Product\ProductInterface;

class CookingProcess extends AbstractCookProcess
{

    public function __construct(private ProductFactory $productFactory)
    {

    }

    protected function cookProduct(string $type, array $additionalIngredients): ProductInterface
    {
        return $this->productFactory->create($type, $additionalIngredients);
    }
}