<?php

declare(strict_types=1);

namespace App\Adapter;

use App\ProductInterface;

class Nuggets implements ProductInterface
{
    private string $name;
    private array $ingredientList;
    private string $customDescription;

    public function __construct(
        ?string $name = null,
    ) {
        $this->name = $name ?? $this->getBaseName();
        $this->ingredientList = $this->getBaseProductList();
        $this->customDescription = $this->getBaseCustomDescription();
    }

    public function getCustomDescription(): string
    {
        return $this->customDescription;
    }

    public function setCustomDescription(string $customDescription): void
    {
        $this->customDescription = $customDescription;
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
            'chicken'
        ];
    }

    public function getBaseName(): string
    {
        return 'nuggets';
    }

    private function getBaseCustomDescription(): string
    {
        return 'Be careful, the nuggets contains chicken';
    }
}
