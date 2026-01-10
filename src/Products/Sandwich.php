<?php declare(strict_types=1);

namespace App\Products;

use App\Core\FoodProductInterface;

class Sandwich implements FoodProductInterface
{

    public function getName(): string
    {
        return 'Сендвич';
    }

    public function getIngredients(): array
    {
        return ['хлеб', 'курица', 'соус'];
    }

    public function getDescription(): string
    {
        return $this->getName() . ", состав: " . implode(", ", $this->getIngredients());
    }
}
