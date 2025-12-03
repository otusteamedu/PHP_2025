<?php
declare(strict_types=1);


namespace Dinargab\Homework15\Model\Product;

interface ProductInterface
{
    public function getName(): string;

    public function getPrice(): int;

    public function getDescription(): string;

    public function getIngredients(): array;
}