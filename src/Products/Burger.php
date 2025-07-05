<?php declare(strict_types=1);

namespace App\Products;

use App\Core\FoodProductInterface;

class Burger implements FoodProductInterface
{

    public function getName(): string
    {
        return 'Бургер';
    }

    public function getIngredients(): array
    {
        return ['котлета', 'специальный соус', 'булочка с кунжутом'];
    }

    public function getDescription(): string
    {
        return $this->getName() . ", состав: " . implode(", ", $this->getIngredients());
    }
}
