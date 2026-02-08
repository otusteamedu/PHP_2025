<?php

namespace Shop\External\Recipes;

use Shop\External\Entity\Pizza;

class PizzaRecipe
{
    public function make(string $type): Pizza
    {
        $pizza = null;
        if ($type === 'tomato') {
            $pizza = new Pizza('Пицца с томатами');
            $pizza->setDough('обычная');
            $pizza->addTopping('tomato');
            $pizza->addTopping('tomato');
        } else if ($type === 'cheese') {
            $pizza = new Pizza('Пицца с томатами');
            $pizza->setDough('обычная');
            $pizza->addTopping('cheese');
            $pizza->addTopping('tomato');
        }

        if(empty($pizza)) {
            throw new \Exception('Не найдена пицца');
        }

        return $pizza;
    }
}