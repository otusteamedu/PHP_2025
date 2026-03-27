<?php

declare(strict_types=1);

namespace App\Builder;

interface HotdogBuilderInterface
{
    public function getName(): string;
    public function getIngredientList(): array;
    public function setIngredientList(array $ingredientList): void;
    public function setName(string $name): void;
    public function getBaseProductList(): array;
    public function getBaseName(): string;
}
