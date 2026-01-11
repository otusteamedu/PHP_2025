<?php declare(strict_types=1);

namespace Fastfood\Products\Entity;

interface ProductInterface
{
    public function getDescription(): string;
    public function getCost(): float;
    public function getBasicIngredients(): array;
}