<?php

namespace app\models;

class Product
{

    public $id_product;
    public $name_product;
    public $description;
    public $price;
    public $name_unit;
    public $img;
    public $type;
    public $category;

    public function __construct($product)
    {
//        var_dump($product);
        $this -> id_product = $product['id_product'];
        $this -> name_product = $product['name_product'];
        $this -> description = $product['description'];
        $this -> price = $product['price'];
        $this -> name_unit = $product['name_unit'];
        $this -> img = $product['img'];
        $this -> type = $product['type'];
        $this -> category = $product['category'];
    }

}