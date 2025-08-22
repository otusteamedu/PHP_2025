<?php

declare(strict_types=1);

namespace App\Adapters;

class Pizza implements PizzaInterface
{
    private string $_crust;
    private string $_sauce;
    private array $_toppings = [];
    private int $_basePrice = 800;

    public function __construct(string $crust = 'тонкое', string $sauce = 'томатный')
    {
        $this->_crust = $crust;
        $this->_sauce = $sauce;
    }

    public string $crust {
        get {
            return $this->_crust;
        }
    }

    public string $sauce {
        get {
            return $this->_sauce;
        }
    }

    public array $toppings {
        get {
            return $this->_toppings;
        }
    }

    public int $basePrice {
        get {
            return $this->_basePrice;
        }
    }

    public function addTopping(string $topping, int $price): void
    {
        $this->_toppings[] = $topping;
        $this->_basePrice += $price;
    }

    public function getTotalCost(): int
    {
        return $this->basePrice;
    }
}
