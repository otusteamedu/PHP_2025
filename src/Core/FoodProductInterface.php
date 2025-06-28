<?php declare(strict_types=1);

namespace App\Core;

interface FoodProductInterface
{
    public function getName(): string;
    public function getIngredients(): array;
    public function getDescription(): string;
}
