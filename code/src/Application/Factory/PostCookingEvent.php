<?php

declare(strict_types=1);

namespace Ak\Hw\Application\Factory;

class PostCookingEvent implements CookingEventInterface
{
    public function handle(string $product): string
    {
        if (str_contains($product, 'соленый огурец')) {
            return "Событие приготовления: $product не соответствует стандарту, утилизируем его...\n";
        }

        return "Событие приготовления: $product готов и соответствует стандарту\n";
    }
}
