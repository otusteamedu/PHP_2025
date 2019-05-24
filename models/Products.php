<?php

namespace app\models;

class Products extends DbModel
{

    public $id_product;
    public $name_product;
    public $description;
    public $price;
    public $img;
//    public $category;
//    public $type;
//    public $name_unit;
    public $id_unit;
    public $id_product_type;
    public $id_product_category;

    public static $condition = "p.id_unit=u.id_unit AND p.id_product_category = c.id_product_category AND p.id_product_type = t.id_product_type";
    public static $params = [];
    public static $columns = "id_product, name_product, price, img, description, category, type, name_unit";


    public function __construct($name = null, $description = null, $price = null, $img=null, $id_category_type=null, $id_unit=null, $id_product_type=null)
    {
        parent::__construct();
        $this->name_product = $name;
        $this->description = $description;
        $this->price = $price;
        $this->img=$img;
//        $this->category=$category;
//        $this->type=$type;
//        $this->name_unit=$name_unit;
        $this->id_unit = $id_unit;
        $this->id_product_type = $id_product_type;
        $this->id_product_category = $id_category_type;
    }

    public static function getTableName()
    {
        return 'products as p, units as u, product_category as c, product_types as t';
    }
    public static function getInsertTableName()
    {
        return 'products';
    }

    public static function getId4Query()
    {
        return 'id_product';
    }
    public static function getParams(){

        return [];
    }
    public function getValues(){

        return ":name_product,:price, :img, :id_unit,:id_product_type, :id_product_category, :description";
    }
    public function getColumns(){

        return "`name_product`, `price`, `img`, `id_unit`, `id_product_type`, `id_product_category`, `description`";
    }

    public function getInsertParams(){

        return ['name_product'=>$this->name_product,'price'=>$this->price,'img'=>$this->img,'id_unit'=>$this->id_unit,'id_product_type'=>$this->id_product_type,'id_product_category'=>$this->id_product_category,'description'=>$this->description];
    }
    public function getId(){

        return 'id_product';
    }
    public function setId($value){

        $this->id_product = $value;
    }
    public  function getUpdateSet(){

        return "`name_product`= '$this->name_product',`price`=$this->price,`img`='$this->img',`id_unit`=$this->id_unit,`id_product_type`=$this->id_product_type,`id_product_category`=$this->id_product_category,`description`='$this->description'";
    }
    public function getUpdateCondition(){

        return "id_product = $this->id_product";
    }
}