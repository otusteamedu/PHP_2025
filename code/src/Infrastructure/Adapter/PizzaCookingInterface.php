<?php

declare(strict_types=1);

namespace Ak\Hw\Infrastructure\Adapter;

interface PizzaCookingInterface
{
    public function prepare(): void;
    public function bake(): void;
    public function cut(): void;
    public function box(): void;
}
