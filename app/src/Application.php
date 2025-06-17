<?php

namespace App;

use App\Cooking\BurgerCookingProcess;
use App\Cooking\PizzaCookingProcess;
use App\Cooking\HotDogCookingProcess;
use App\Cooking\SandwichCookingProcess;
use App\Decorator\WithCheese;
use App\Decorator\WithLettuce;
use App\Decorator\WithOnion;
use App\Decorator\WithPepper;
use App\Product\Food;
use App\Container\Container;

class Application
{
    public function __construct(private Container $container) {}

    public function run(string $mode, string $product, array $toppings = []): void
    {
        $foodFactory = $this->container->get('foodFactory.' . $product);

        $food = $foodFactory->createFood();
        $food = $this->applyToppings($food, $toppings);

        $process = $this->getCookingProcess($food);
        $process->process();

        $orderFactory = $this->container->get('orderFactory.' . $mode);
        $order = $orderFactory->createOrder([$food]);
        $order->process();
    }

    private function applyToppings(Food $food, array $toppings): Food
    {
        foreach ($toppings as $topping) {
            $food = match ($topping) {
                'cheese' => new WithCheese($food),
                'lettuce' => new WithLettuce($food),
                'onion' => new WithOnion($food),
                'pepper' => new WithPepper($food),
                default => $food,
            };
        }
        return $food;
    }

    private function getCookingProcess(Food $food)
    {
        return match ($food->getType()) {
            'pizza' => new PizzaCookingProcess($food),
            'hotdog' => new HotDogCookingProcess($food),
            'sandwich' => new SandwichCookingProcess($food),
            'burger' => new BurgerCookingProcess($food),
            default => throw new \RuntimeException("Неизвестный тип продукта: " . $food->getType()),
        };
    }
}
