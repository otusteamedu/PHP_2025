<?php

declare(strict_types=1);

namespace Restaurant\Command;

use Restaurant\Builder\BurgerBuilder;
use Restaurant\Decorator\CheeseDecorator;
use Restaurant\Proxy\QualityCheckProxy;

readonly class QualityCheckCommand
{
    public function __construct(private BurgerBuilder $builder)
    {
    }

    public function execute(): void
    {
        echo "=== Демонстрация проверки качества через Proxy ===\n\n";

        // Создаем несколько продуктов и проверяем их качество
        for ($i = 1; $i <= 3; $i++) {
            echo "--- Продукт #{$i} ---\n";

            $burger = $this->builder->createBurger();
            $burger = new CheeseDecorator($burger);

            $proxy = new QualityCheckProxy($burger);

            echo "Описание: {$proxy->getDescription()}\n";
            echo "Цена: {$proxy->getPrice()} руб.\n\n";

            $proxy->cook();

            if ($proxy->isDisposed()) {
                echo "Продукт был утилизирован\n";
            } else {
                echo "Продукт готов к выдаче\n";
            }

            echo "\n";
        }
    }
}
