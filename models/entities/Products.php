<?php

namespace app\models\entities;

use app\engine\Db;
use app\models\entities\DataEntity;
use mysql_xdevapi\Exception;

class Products extends DataEntity
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
    public function getId(){

        return $this->id_product;
    }
    public function getIdName(){

        return 'id_product';
    }
}