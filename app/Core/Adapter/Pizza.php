<?php

declare(strict_types=1);

namespace App\Core\Adapter;

class Pizza implements PizzaInterface
{
    /**
     * @return string
     */
    public function makePizza(): string
    {
        return "Making pizza with traditional method";
    }
}