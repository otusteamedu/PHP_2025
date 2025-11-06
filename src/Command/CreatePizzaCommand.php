<?php

declare(strict_types=1);

namespace Restaurant\Command;

use Restaurant\Adapter\ItalianPizza;
use Restaurant\Adapter\PizzaAdapter;

class CreatePizzaCommand
{
    public function execute(): void
    {
        echo "=== Создание пиццы через адаптер ===\n\n";

        $italianPizza = new ItalianPizza('большая');
        $pizza = new PizzaAdapter($italianPizza);

        echo "Продукт: {$pizza->getDescription()}\n";
        echo "Цена: {$pizza->getPrice()} руб.\n\n";

        $pizza->cook();

        echo "\n+Пицца готова!\n";
    }
}
