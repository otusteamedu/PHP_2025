<?php

namespace app\models;

class Products extends DbModel
{

    public $id_product;
    public $name_product;
    public $description;
    public $price;


    public function __construct($id = null, $name = null, $description = null, $price = null)
    {
        parent::__construct();
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }

    public static function getTableName()
    {
        return 'products';
    }

    public static function getId4Query()
    {
        return 'id_product';
    }

}