<?php

namespace App\Adapters;

interface PizzaInterface
{
    public function prepare(): string;
    public function getIngredients(): array;
}