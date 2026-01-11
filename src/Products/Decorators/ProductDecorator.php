<?php declare(strict_types=1);

namespace Fastfood\Products\Decorators;

use Fastfood\Products\Entity\ProductInterface;

abstract class ProductDecorator implements ProductInterface
{
    protected ProductInterface $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function getCost(): float
    {
        return $this->product->getCost() + $this->getPrice();
    }

    public function getDescription(): string
    {
        return $this->product->getDescription() . ', ' . $this->getName();
    }

    public function getBasicIngredients(): array
    {
        return $this->product->getBasicIngredients();
    }

    public function getQualityInfo(): array
    {
        // Merge quality info from the decorated product
        $qualityInfo = $this->product->getQualityInfo();
        
        // Add decorator-specific quality info if needed
        // For now, we just pass through the existing info
        return $qualityInfo;
    }

    abstract public function getName(): string;
    abstract public function getPrice(): float;
}