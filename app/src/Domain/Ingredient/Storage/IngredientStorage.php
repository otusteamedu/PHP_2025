<?php

namespace App\Domain\Ingredient\Storage;

use App\Domain\Ingredient\IngredientInterface;

class IngredientStorage implements IngredientStorageInterface
{
    /** @var IngredientInterface[] */
    private array $ingredients = [];

    /**
     * @param IngredientInterface[] $ingredients
     */
    public function __construct(array $ingredients = [])
    {
        foreach ($ingredients as $ingredient) {
            $this->ingredients[$ingredient->getName()] = $ingredient;
        }
    }

    public function get(string $name): IngredientInterface
    {
        if (!isset($this->ingredients[$name])) {
            throw new \RuntimeException("Ingredient {$name} not found");
        }
        return $this->ingredients[$name];
    }

    public function has(string $name, int $count): bool
    {
        return isset($this->ingredients[$name])
            && $this->ingredients[$name]->getCount() >= $count;
    }

    public function take(string $name, int $count): void
    {
        $ingredient = $this->get($name);
        $ingredient->remove($count);
    }

    public function add(IngredientInterface $ingredient): void
    {
        $ingredientName = $ingredient->getName();

        if ($this->has($ingredientName, 0)) {
            $this->ingredients[$ingredientName]->add($ingredient->getCount());
            return;
        }
        $this->ingredients[$ingredientName] = $ingredient;
    }
}