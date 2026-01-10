<?php declare(strict_types=1);

namespace App\Products;

use App\Core\FoodProductInterface;

class HotDog implements FoodProductInterface
{

    public function getName(): string
    {
        return 'Хот-дог';
    }

    public function getIngredients(): array
    {
        return ['сосиска', 'кетчуп', 'горчица', 'булочка'];
    }

    public function getDescription(): string
    {
        return $this->getName() . ", состав: " . implode(", ", $this->getIngredients());
    }
}
