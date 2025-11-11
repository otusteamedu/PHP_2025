<?php

declare(strict_types=1);

namespace App\Builder;

use App\ProductInterface;

class HotdogBuilder implements HotdogBuilderInterface
{
    private string $name;
    private array $ingredientList;

    public function getName(): string
    {
        return $this->name;
    }

    public function getIngredientList(): array
    {
        return $this->ingredientList;
    }

    public function setName(?string $name = null): void
    {
        if ($name === null) {
            $name = $this->getBaseName();
        }

        $this->name = $name;
    }

    public function setIngredientList(array $ingredientList, bool $ignoreBaseList = false): void
    {
        if ($ignoreBaseList === true) {
            $this->ingredientList = $ingredientList;
        } else {
            $this->ingredientList = array_unique(array_merge($this->getBaseProductList(), $ingredientList));
        }
    }

    public function build(): ProductInterface
    {
        return new Hotdog($this);
    }

    public function getBaseProductList(): array
    {
        return [
            'bun',
            'sausage',
            'baseSauce'
        ];
    }

    public function getBaseName(): string
    {
        return 'hotdog';
    }
}
