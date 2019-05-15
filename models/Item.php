<?php

namespace app\models;

abstract class Item{

    private $name; // Название товара
    private $description; // Краткое описание
    private $price; // Цена
    static $marja=0;
    private $tax = 1;

    /**
     * Product constructor.
     * @param $name
     * @param $description
     * @param $price
     */
    public function __construct($input)
    {
        $this->name = $input["name"];
        $this->description = $input["description"];
        $this->price = $input["price"];
    }

    public function __toString()
    {
        $str = "Название: " . $this->name . " <br> " . "Цена: " . $this->price;
//        var_dump($str); die();
        return $str;
    }
    public function marja(){

        return self::$marja;
    }
    public function getPrice(){

        return $this->price;
    }
    public function getTax(){

        return $this->tax;
    }
}