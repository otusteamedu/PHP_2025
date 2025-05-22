<?php
namespace App\Decorator;

use App\Products\ProductInterface;

class ProductWithAdditive implements ProductInterface
{
    private $product;
    private $additive;
    
    public function __construct(ProductInterface $product, string $additive)
    {
        $this->product = $product;
        $this->additive = $additive;
    }
    
    public function getName(): string
    {
        return $this->product->getName() . " с " . $this->additive;
    }
    
    public function getPrice(): float
    {
        return $this->product->getPrice() + 0.5;
    }
    
    public function getDescription(): string
    {
        return $this->product->getDescription() . ", " . $this->additive;
    }
}
