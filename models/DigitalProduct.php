<?php

namespace app\Models;


class DigitalProduct extends Item
{
    private $tax = 0.5;
//    static $marja=0;

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
//    public function marja(){
//
//        return self::$marja;
//    }
}