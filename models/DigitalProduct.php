<?php

namespace app\Models;


class DigitalProduct extends Product
{
    private $tax = 0.5;

    public function __construct(array $input)
    {
        parent::__construct($input);
    }
    public function getTax(){

        return $this->tax;
    }
    public function sell($quantity){

        self::$marja += $this->getTax()*$quantity*$this->getPrice();
    }
}