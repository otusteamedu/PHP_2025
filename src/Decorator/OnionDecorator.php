<?php declare(strict_types=1);

namespace App\Decorator;

class OnionDecorator extends IngredientDecorator
{
    public function getIngredients(): array
    {
        return array_merge($this->product->getIngredients(), ['лук']);
    }
}
