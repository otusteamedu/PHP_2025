<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\CookingStrategy;

class BurgerCookingStrategy implements CookingStrategyInterface
{
    public function cook(string $productName): string
    {
        $baseProduct = 'Готовим ' . $productName;
        $product = $baseProduct . ' с котлетой, салатом и соусом';
        return $product . ' (упаковано)';
    }
}
