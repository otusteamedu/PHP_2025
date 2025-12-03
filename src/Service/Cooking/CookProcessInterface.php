<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Cooking;

use Dinargab\Homework15\Model\Product\ProductInterface;

interface CookProcessInterface
{
    public function cook(string $type, array $additionalIngredients): ProductInterface;
}