<?php

declare(strict_types=1);

namespace Ak\Hw\Domain\CookingStrategy;

class HotDogCookingStrategy implements CookingStrategyInterface
{
    public function cook(string $productName): string
    {
        $baseProduct = 'Готовим ' . $productName;
        $product = $baseProduct . ' с сосиской, кетчупом и горчицей';
        return $product . ' (упаковано)';
    }
}
