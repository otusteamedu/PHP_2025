<?php

declare(strict_types=1);

namespace App\Decorator;

use App\Builder\HotdogBuilder;
use App\Builder\HotdogBuilderInterface;
use App\ProductInterface;

class ExtraCheeseHotdogBuilderDecorator implements HotdogBuilderInterface
{
    public function __construct(
        private HotdogBuilder $baseBuilder,
    ) {
    }

    public function getName(): string
    {
        return $this->baseBuilder->getName();
    }

    public function getIngredientList(): array
    {
        return $this->baseBuilder->getIngredientList();
    }

    public function setName(?string $name = null): void
    {
        if ($name === null) {
            $name = $this->getBaseName();
        }

        $this->baseBuilder->setName($name);
    }

    public function setIngredientList(array $ingredientList, bool $ignoreBaseList = false): void
    {
        $this->baseBuilder->setIngredientList(
            array_unique(array_merge($this->getBaseProductList(), $ingredientList)),
            true,
        );
    }

    public function build(): ProductInterface
    {
        return $this->baseBuilder->build();
    }

    public function getBaseProductList(): array
    {
        $ingredientList = $this->baseBuilder->getBaseProductList();

        $ingredientList[] = 'cheese';
        $ingredientList[] = 'cheddar';
        $ingredientList[] = 'cheeseSauce';

        return $ingredientList;
    }

    public function getBaseName(): string
    {
        return 'extra cheese ' . $this->baseBuilder->getBaseName();
    }
}
