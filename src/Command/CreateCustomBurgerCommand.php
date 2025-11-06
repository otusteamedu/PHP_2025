<?php

declare(strict_types=1);

namespace Restaurant\Command;

use Restaurant\Builder\BurgerBuilder;
use Restaurant\Decorator\LettuceDecorator;
use Restaurant\Decorator\CheeseDecorator;
use Restaurant\Decorator\BaconDecorator;
use Restaurant\Decorator\TomatoDecorator;
use Restaurant\Decorator\OnionDecorator;

readonly class CreateCustomBurgerCommand
{
    public function __construct(private BurgerBuilder $builder)
    {
    }

    public function execute(): void
    {
        echo "=== Создание кастомного бургера ===\n\n";

        $burger = $this->builder->createBurger();

        // Добавляем ингредиенты через декораторы
        $burger = new LettuceDecorator($burger);
        $burger = new CheeseDecorator($burger);
        $burger = new BaconDecorator($burger);
        $burger = new TomatoDecorator($burger);
        $burger = new OnionDecorator($burger);

        echo "Продукт: {$burger->getDescription()}\n";
        echo "Цена: {$burger->getPrice()} руб.\n\n";

        $burger->cook();

        echo "\n+Кастомный бургер готов!\n";
    }
}
