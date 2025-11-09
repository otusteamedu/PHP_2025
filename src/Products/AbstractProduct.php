<?php

declare(strict_types=1);

namespace App\Products;

abstract class AbstractProduct implements ProductInterface
{
    private string $_name;
    private int $_price;
    private string $_description;
    
    public string $name {
        get {
            return $this->_name;
        }
        set {
            $this->_name = $value;
        }
    }
    public int $price {
        get {
            return $this->_price;
        }
        set {
            $this->_price = $value;
        }
    }
    public string $description {
        get {
            return $this->_description;
        }
        set {
            $this->_description = $value;
        }
    }
}
