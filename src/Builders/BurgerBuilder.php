<?php

declare(strict_types=1);

namespace App\Builders;

use App\Products\ProductWithIngredients;

class BurgerBuilder implements ProductBuilderInterface
{
    private ProductWithIngredients $product;

    public function __construct()
    {
        $this->product = new ProductWithIngredients();
        $this->product->name = 'Custom Burger';
        $this->product->price = 550;
        $this->product->description = 'Кастомный бургер';
    }

    public function addLettuce(): self
    {
        $this->product->addIngredient('салат', 25);

        return $this;
    }

    public function addTomato(): self
    {
        $this->product->addIngredient('помидор', 30);

        return $this;
    }

    public function addCheese(): self
    {
        $this->product->addIngredient('сыр', 50);

        return $this;
    }

    public function addOnion(): self
    {
        $this->product->addIngredient('лук', 20);

        return $this;
    }

    public function addBacon(): self
    {
        $this->product->addIngredient('бекон', 100);

        return $this;
    }

    public function addPickles(): self
    {
        $this->product->addIngredient('огурцы', 15);

        return $this;
    }

    public function addMustard(): self
    {
        $this->product->addIngredient('горчица', 10);

        return $this;
    }

    public function addKetchup(): self
    {
        $this->product->addIngredient('кетчуп', 10);

        return $this;
    }

    public function build(): ProductWithIngredients
    {
        return $this->product;
    }
}
