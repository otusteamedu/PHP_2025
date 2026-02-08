<?php

declare(strict_types=1);

namespace Ak\Hw\Infrastructure\Adapter;

class PizzaCooker implements PizzaCookingInterface
{
    public function prepare(): string
    {
        return "Preparing pizza...";
    }

    public function bake(): string
    {
        return "Baking pizza...";
    }

    public function cut(): string
    {
        return "Cutting pizza...";
    }

    public function box(): string
    {
        return "Boxing pizza...";
    }
}
