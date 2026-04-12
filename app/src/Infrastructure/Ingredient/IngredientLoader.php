<?php

namespace App\Infrastructure\Ingredient;

use App\Domain\Ingredient\IngredientInterface;

readonly class IngredientLoader
{
    public function __construct(
        private IngredientFactory $factory
    )
    {

    }

    /** @return IngredientInterface[] */
    public function loadFromFile(string $path): array
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Config not found: {$path}");
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \RuntimeException("Invalid JSON config");
        }

        $result = [];

        foreach ($data as $name => $ingredientInfo) {
            $count = $ingredientInfo['count'] ?: 0;
            $result[] = $this->factory->create($name, $count);
        }

        return $result;
    }
}