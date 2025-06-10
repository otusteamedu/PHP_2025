<?php
declare(strict_types=1);

namespace App\Domain\Patterns\Decorator;

interface IngredientDecoratorInterface
{
    public function getTitle(): string;
    public function getPrice(): float;
}