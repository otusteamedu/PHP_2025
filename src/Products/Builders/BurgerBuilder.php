<?php declare(strict_types=1);

namespace Fastfood\Products\Builders;

use Fastfood\Products\Entity;

class BurgerBuilder implements ProductBuilderInterface
{
    private Entity\Burger $burger;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->burger = new Entity\Burger();
    }

    /**
     * @return void
     */
    public function setBase(): void
    {
        $this->burger->setBase($this->burger->getDescription(), $this->burger->getCost());
    }

    /**
     * @return array
     */
    public function getBasicIngredients(): array
    {
        $product = $this->burger;
        return $product->getBasicIngredients();
    }

    public function getProduct(): Entity\ProductInterface
    {
        $product = $this->burger;
        $this->reset();
        return $product;
    }
}