<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\CookingStrategy;

class PizzaCookingStrategy implements CookingStrategyInterface
{
    public function cook(string $productName): string
    {
        $baseProduct = 'Готовим ' . $productName;
        $product = $baseProduct . ' с пепперони, моцареллой и томатным соусом';
        return $product . ' (упаковано)';
    }
}
