<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\CookingStrategy;

class SandwichCookingStrategy implements CookingStrategyInterface
{
    public function cook(string $productName): string
    {
        $baseProduct = 'Готовим ' . $productName;
        $product = $baseProduct . ' с ветчиной, сыром и овощами';
        return $product . ' (упаковано)';
    }
}
