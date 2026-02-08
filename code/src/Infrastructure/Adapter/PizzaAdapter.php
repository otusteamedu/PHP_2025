<?php

declare(strict_types=1);

namespace Ak\Hw\Infrastructure\Adapter;

use Ak\Hw\Domain\CookingStrategy\CookingStrategyInterface;

class PizzaAdapter implements CookingStrategyInterface
{
    public function __construct(private PizzaCooker $pizzaCooker)
    {
    }

    public function cook(string $productName): string
    {
        $messages = [
            'Готовим ' . $productName,
            $this->pizzaCooker->prepare(),
            $this->pizzaCooker->bake(),
            $this->pizzaCooker->cut(),
            $this->pizzaCooker->box(),
        ];
        return implode("\n", $messages);
    }
}
