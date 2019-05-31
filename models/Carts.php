<?php

namespace app\models;
use app\controllers\Controller;
use app\models\DbModel;
use app\engine\Db;

class Carts extends DbModel
{
    protected $id_cart;
    protected $id_product;
    protected $id_user;
    protected $id_session;
    protected $quantity;
    protected $changes=[];

    public function __construct($id_cart=null,$id_product = null, $id_user = null, $id_session = null, $quantity = null)
    {
        parent::__construct();
        $this->id_cart = $id_cart;
        $this->id_product = $id_product;
        $this->id_user = $id_user;
        $this->id_session = $id_session;
        $this->quantity = $quantity;
    }

    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public static function getTableName()
    {
        return 'carts';
    }

    public static function getAll(){

        $sql = "SELECT id_cart,p.id_product,name_product,price,img,description, category, name_unit, type, quantity FROM carts as c, products as p, units as u, product_category as ctg,product_types as t WHERE id_session = :id_session AND c.id_product=p.id_product AND ctg.id_product_category=p.id_product_category AND u.id_unit=p.id_unit AND t.id_product_type=p.id_product_type";
        $params = [':id_session' => session_id()];
//      var_dump($sql,$params);
       return Db::getInstance()->queryAll($sql, $params);
    }
    public function getId(){

        return $this->id_cart;
    }
    public function getIdProduct()
    {
        return $this->id_product;
    }

    public static function delete($id)
    {
        $tableName = static::getTableName();
        $sql = "DELETE FROM {$tableName} WHERE id_cart = :id";
        Db::getInstance()->execute($sql, ["id" => $id]);
    }
    public static function getCount($id){

        $tableName = static::getTableName();
        $session_id = session_id();
        $sql = "SELECT count(*) as count FROM $tableName WHERE id_product= :id AND id_session = :id_session";
//        var_dump($sql,$session_id,$id);
        return Db::getInstance()->queryAll($sql, [':id'=>$id, ':id_session'=>$session_id]);
    }
    public static function getOne($id)
    {
        $tableName = static::getTableName();
        $id_session = session_id();
        $sql = "SELECT * FROM {$tableName} WHERE id_product = :id AND id_session = :id_session";
        return Db::getInstance()->queryObject($sql, [':id' => $id, ':id_session'=>$id_session], static::class);
    }
    public static function getGoodsQuantity(){

        $tableName = static::getTableName();
        $id_session = session_id();
        $sql = "SELECT count(*) as count FROM {$tableName} WHERE id_session = :id_session";

        return Db::getInstance()->queryOne($sql, [':id_session'=>$id_session], static::class);
    }
    public function update(){

        $params = [];
        $set = [];

        $params[':id'] = (int)$this->getId();
        $params[':id_session'] = session_id();
        $params[':id_product'] = $this->getIdProduct();
        $params[':quantity'] = $this->getQuantity();

        $sql = "UPDATE `carts` SET `id_product`= :id_product,`quantity`= :quantity WHERE id_cart=:id AND id_session = :id_session";
//        var_dump($sql, $params,$this->changes);

        Db::getInstance()->execute($sql, $params);
    }
}