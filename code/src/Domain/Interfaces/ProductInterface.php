<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface ProductInterface
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): string;

    public function getPrice(): float;

    public function getIngredients(): array;

    public function getAdditions(): array;

    public function toArray(): array;
}
