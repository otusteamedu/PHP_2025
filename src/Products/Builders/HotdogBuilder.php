<?php declare(strict_types=1);

namespace Fastfood\Products\Builders;

use Fastfood\Products\Entity;

class HotdogBuilder implements ProductBuilderInterface
{
    private Entity\Hotdog $hotdog;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->hotdog = new Entity\Hotdog();
    }

    public function setBase(): void
    {
        $this->hotdog->setBase($this->hotdog->getDescription(), $this->hotdog->getCost());
    }

    public function getBasicIngredients(): array
    {
        $product = $this->hotdog;
        return $product->getBasicIngredients();
    }

    public function getProduct(): Entity\ProductInterface
    {
        $product = $this->hotdog;
        $this->reset();
        return $product;
    }
}