<?php

declare(strict_types=1);

namespace App\Adapter;

use App\ProductInterface;

class Pizza implements ProductInterface, PizzaInterface
{
    private string $name;
    private array $ingredientList;
    private string $description;

    public function __construct(
        ?string $name = null,
        array $ingredientList = [],
        string $description = '',
    ) {
        $this->name = $name ?? $this->getBaseName();
        $this->ingredientList = array_unique(array_merge($this->getBaseProductList(), $ingredientList));
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIngredientList(): array
    {
        return $this->ingredientList;
    }

    public function setIngredientList(array $ingredientList): void
    {
        $this->ingredientList = $ingredientList;
    }

    public function getBaseProductList(): array
    {
        return [
            'dough',
            'sauce',
            'cheese'
        ];
    }

    public function getBaseName(): string
    {
        return 'pizza';
    }
}
