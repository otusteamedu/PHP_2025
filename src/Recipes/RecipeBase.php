<?php

namespace Shop\Recipes;

use Exception;
use Shop\Ingredients\BaseProduct\Base;
use Shop\Ingredients\Component;
use Shop\Ingredients\Meat\BaseMeat;
use Shop\Ingredients\Product;
use Shop\Ingredients\Sauce\BaseSauce;

abstract class RecipeBase
{

    protected abstract function getBase(): Base;
    protected abstract function getFillingCollection(): array;
    protected abstract function getMeat(): BaseMeat;
    protected abstract function getSauce(): ?BaseSauce;
    protected abstract function needDuplicateBase(): bool;
    public abstract function getDescription(): string;

    public function cook(array $extraComponents = []): Product
    {
        $product = $this->getProduct();
        $product->addComponent($this->getBase());
        $product->addComponent($this->getMeat());

        if ($extraComponents) {
            foreach ($extraComponents as $extraComponent) {
                if ($extraComponent instanceof Component) {
                    $product->addComponent($extraComponent);
                }
            }
        }

        $components = $this->getFillingCollection();

        foreach ($components as $component) {
            $product->addComponent($component);
        }
        if ($this->getSauce()) {
            $product->addComponent($this->getSauce());
        }

        if ($this->needDuplicateBase()) {
            $product->addComponent($this->getBase());
        }

        $this->afterCook($product);
        return $product;
    }

    private function getProduct()
    {
        return new Product($this->getDescription());
    }

    public function getPrice(): float
    {
        $price = 0;
        $base = $this->getBase();
        if ($base !== null) {
            $price += $base->getPrice();
        }

        $meat = $this->getMeat();

        if ($meat !== null) {
            $price += $meat->getPrice();
        }

        $sauce = $this->getSauce();
        if ($sauce !== null) {
            $price += $sauce->getPrice();
        }


        $components = $this->getFillingCollection();

        foreach ($components as $component) {
            if (!$component instanceof Component) {
                throw new Exception('Invalid component');
            }
            $price += $component->getPrice();
        }

        if ($this->needDuplicateBase()) {
            $price += $this->getBase()->getPrice();
        }
        return $price;
    }

    private function meetsQualityStandards(Product $product): bool
    {
        return count($product->getComponents()) > 0;
    }

    public function afterCook(Product $product): void
    {
        if (!$this->meetsQualityStandards($product)) {
            throw new \Exception('Бургер собран неправильно!');
        }
    }
}