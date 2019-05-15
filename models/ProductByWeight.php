<?php

namespace app\models;

class ProductByWeight extends Item{

    static $marja=0;
    /**
     * Product constructor.
     * @param $input ассоциативный массив, где ключи name, price, description
     */
    public function __construct(array $input)
    {
        parent::__construct($input);
    }

    public function sell ($weight){

        self::$marja += $this->getTax()*$weight*$this->getPrice();
    }
    public function marja(){

        return self::$marja;
    }

}