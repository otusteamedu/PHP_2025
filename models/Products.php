<?php

namespace app\models;

class Products extends DbModel
{

    public $id_product;
    public $name_product;
    public $description;
    public $price;
    public static $condition = "p.id_unit=u.id_unit AND p.id_product_category = c.id_product_category AND p.id_product_type = t.id_product_type";
    public static $params = [];
    public static $columns = "id_product, name_product, price, img, description, category, type, name_unit";


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
        return 'products as p, units as u, product_category as c, product_types as t';
    }

    public static function getId4Query()
    {
        return 'id_product';
    }

}