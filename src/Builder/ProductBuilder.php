<?php

namespace App\Builder;

use App\Model\Product;
use InvalidArgumentException;

class ProductBuilder 
{
    private $prototypes = ['burger', 'sandwich', 'hotdog'];
    
    public function build(string $type): Product 
    {
        if (in_array($type, $this->prototypes)) {
            $class = 'App\\Model\\' . ucfirst($type);
            return new $class();
        }

        $adapterClass = 'App\\Adapter\\' . ucfirst($type) . 'Adapter';
        if (class_exists($adapterClass)) {
            $className = 'App\\Model\\' . ucfirst($type);
            $product = new $className;
            $product = new $adapterClass($product);
            return $product;
        } 
          
        throw new InvalidArgumentException("Неизвестный тип продукта: $type");        
    }
}