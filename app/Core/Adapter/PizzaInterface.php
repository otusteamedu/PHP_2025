<?php

declare(strict_types=1);

namespace App\Core\Adapter;

interface PizzaInterface
{
    public function makePizza(): string;
}