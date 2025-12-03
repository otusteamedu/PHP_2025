<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Builder;

use Dinargab\Homework15\Model\Product\Burger;

class BurgerBuilder implements BuilderInterface
{
    private Burger $burger;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->burger = new Burger();
    }


    public function build(): Burger
    {
        $burger = $this->burger;
        $this->reset();
        return $burger;
    }


    public function addFilling(string $filling): BuilderInterface
    {
        $this->burger->toppings[] = $filling;
        return $this;
    }

    public function addMainIngredients(): BuilderInterface
    {
        $this->burger->bun = "Burger Bun";
        $this->burger->patty = "Burger Patty";
        return $this;
    }

}