<?php declare(strict_types=1);

namespace Fastfood\Products\Decorators;

interface IngredientDecoratorInterface
{
    public function getKey(): string;
    public function getName(): string;
    public function getPrice(): float;
}