<?php

declare(strict_types=1);

namespace Restaurant\Command;

use Restaurant\Builder\BurgerBuilder;

readonly class CreateBurgerCommand
{
    public function __construct(private BurgerBuilder $builder)
    {
    }

    public function execute(): void
    {
        echo "=== Создание простого бургера ===\n\n";

        $burger = $this->builder->createBurger();

        echo "Продукт: {$burger->getDescription()}\n";
        echo "Цена: {$burger->getPrice()} руб.\n\n";

        $burger->cook();

        echo "\n+Бургер готов!\n";
    }
}
