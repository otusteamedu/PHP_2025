<?php declare(strict_types=1);

namespace Fastfood\Products\Builders;

use Fastfood\Products\Entity;

interface ProductBuilderInterface
{
    public function setBase(): void;

    public function getProduct(): Entity\ProductInterface;

    public function getBasicIngredients(): array;
}