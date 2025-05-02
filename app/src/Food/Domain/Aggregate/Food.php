<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate;

use App\Food\Domain\Aggregate\VO\FoodTitle;
use Ramsey\Uuid\Uuid;

abstract class Food implements FoodInterface
{
    private readonly string $id;
    private array $ingredients = [];

    public function __construct(
        protected FoodTitle $title,
    )
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): FoodTitle
    {
        return $this->title;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function addIngredient(FoodIngredient $ingredient): void
    {
        $this->ingredients[] = $ingredient;
    }

    public function removeIngredient(FoodIngredient $ingredient): void
    {
        if (in_array($ingredient, $this->ingredients)) {
            unset($this->ingredients[array_search($ingredient, $this->ingredients)]);
        }
    }
}