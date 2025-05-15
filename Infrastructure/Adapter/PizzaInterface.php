<?php

declare(strict_types=1);

namespace Infrastructure\Adapter;

interface PizzaInterface
{
    public function makePizza(): string;
}