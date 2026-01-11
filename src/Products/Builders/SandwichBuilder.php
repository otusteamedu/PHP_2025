<?php declare(strict_types=1);

namespace Fastfood\Products\Builders;

use Fastfood\Products\Entity;

class SandwichBuilder implements ProductBuilderInterface
{
    private Entity\Sandwich $sandwich;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->sandwich = new Entity\Sandwich();
    }

    public function setBase(): void
    {
        $this->sandwich->setBase($this->sandwich->getDescription(), $this->sandwich->getCost());
    }

    public function getBasicIngredients(): array
    {
        $product = $this->sandwich;
        return $product->getBasicIngredients();
    }

    public function getProduct(): Entity\ProductInterface
    {
        $product = $this->sandwich;
        $this->reset();
        return $product;
    }
}