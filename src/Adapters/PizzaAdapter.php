<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Products\ProductInterface;

class PizzaAdapter implements ProductInterface
{
    public function __construct(
        private PizzaInterface $pizza
    ) {
    }

    public string $name {
        get {
            $toppings = $this->pizza->toppings;
            $toppingsList = empty($toppings) ? 'Маргарита' : implode(', ', $toppings);
            
            return "Пицца {$toppingsList}";
        }
    }

    public int $price {
        get {
            return $this->pizza->getTotalCost();
        }
    }

    public string $description {
        get {
            $toppings = $this->pizza->toppings;
            $toppingsList = empty($toppings) ? 'классическая' : 'с ' . implode(', ', $toppings);
            
            return "Пицца на {$this->pizza->crust} тесте с {$this->pizza->sauce} соусом ({$toppingsList})";
        }
    }

    public function addToppings(array $toppings): self
    {
        foreach ($toppings as $topping) {
            $price = match($topping) {
                'pepperoni' => 150,
                'mushrooms' => 100,
                'olives' => 80,
                'cheese' => 120,
                'ham' => 200,
                default => 50
            };
            $this->pizza->addTopping($topping, $price);
        }
        return $this;
    }
}
