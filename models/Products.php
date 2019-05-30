<?php

namespace app\models;
use app\engine\Db;

class Products extends DbModel
{

    protected $id_product;
    protected $name_product;
    protected $description;
    protected $price;
    protected $img;
    protected $id_unit;
    protected $id_product_type;
    protected $id_product_category;
    protected $changes=[];

    public function __construct($name = null, $description = null, $price = null, $img=null, $id_category_type=null, $id_unit=null, $id_product_type=null)
    {
        parent::__construct();
        $this->name_product = $name;
        $this->description = $description;
        $this->price = $price;
        $this->img=$img;
        $this->id_unit = $id_unit;
        $this->id_product_type = $id_product_type;
        $this->id_product_category = $id_category_type;
    }

    public function getImg()
    {
        return $this->img;
    }

    public function setImg($img): void
    {
        $this->img = $img;
    }

    public function getNameProduct()
    {
        return $this->name_product;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public static function getTableName()
    {
        return 'products';
    }
    public static function getAll(){

        $sql = "SELECT id_product, name_product, `price`, `img`, `description` FROM products as p, units as u, product_category as c, product_types as t WHERE p.id_unit=u.id_unit AND p.id_product_category = c.id_product_category AND p.id_product_type = t.id_product_type";
//        var_dump($sql);
        return Db::getInstance()->queryAll($sql);
    }
    public static function getOne($id){

        $sql = "SELECT id_product, `name_product`, `price`, `img`, `description` FROM products as p WHERE id_product = :id";
//        var_dump($sql,$id);
        return Db::getInstance()->queryObject($sql, ['id' => $id], static::class);
    }
    public function getId(){

        return $this->id_product;
    }
}