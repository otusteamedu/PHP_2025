<?php

namespace app\models;
use app\engine\Db;

class Products extends DbModel
{

    public $id_product;
    public $name_product;
    public $description;
    public $price;
    public $img;
    public $id_unit;
    public $id_product_type;
    public $id_product_category;

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

        $tableName = static::getTableName();
        $sql = "SELECT id_product, `name_product`, `price`, `img`, `description` FROM products as p WHERE id_product = :id";
//        var_dump($sql,$id);
        return Db::getInstance()->queryObject($sql, ['id' => $id], static::class);
    }
    public function update(){

        $params = [];
        $set = [];

        foreach ($this as $key => $value) {

            if ($key == "db" || $key == "id") continue;
            $params[":{$key}"] = $value;
            $set[] = "`$key`=:{$key}";
        }
        $params[':id'] = $this->id_product;

        $set = implode(", ", $set);
        $value = implode(", ", array_keys($params));

        $sql = "UPDATE `products` SET {$set} WHERE 'id_product'=:id";
        var_dump($sql,$params);
        Db::getInstance()->execute($sql, $params);
    }
//
}