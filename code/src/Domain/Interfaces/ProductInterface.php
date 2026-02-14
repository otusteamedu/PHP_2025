<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface ProductInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function getPrice(): float;

    public function getIngredients(): array;

    public function toArray(): array;
}
