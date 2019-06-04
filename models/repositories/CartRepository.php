<?php

namespace app\models\repositories;


use app\models\Repository;
use app\models\entities\DataEntity;

class CartRepository extends Repository
{

    public function getTableName()
    {
        return 'carts';
    }

    public function getAll(){

        $sql = "SELECT id_cart,p.id_product,name_product,price,img,description, category, name_unit, type, quantity FROM carts as c, products as p, units as u, product_category as ctg,product_types as t WHERE id_session = :id_session AND c.id_product=p.id_product AND ctg.id_product_category=p.id_product_category AND u.id_unit=p.id_unit AND t.id_product_type=p.id_product_type";
        $params = [':id_session' => session_id()];
//      var_dump($sql,$params);
        return $this->db->queryAll($sql, $params);
    }
    public function getId(){

        return $this->id_cart;
    }
    public function getIdProduct()
    {
        return $this->id_product;
    }

    public function delete(DataEntity $entity)
    {
        $tableName = $this->getTableName();
        $sql = "DELETE FROM {$tableName} WHERE id_cart = :id";
        $this->db->execute($sql, ["id" => $entity->id_cart]);
    }
    public function getCount($id){

        $tableName = $this->getTableName();
        $session_id = session_id();
        $sql = "SELECT count(*) as count FROM $tableName WHERE id_product= :id AND id_session = :id_session";
//        var_dump($sql,$session_id,$id);
        return $this->db->queryAll($sql, [':id'=>$id, ':id_session'=>$session_id]);
    }
    public function getOne($id)
    {
        $tableName = $this->getTableName();
        $id_session = session_id();
        $sql = "SELECT * FROM {$tableName} WHERE id_product = :id AND id_session = :id_session";

        return $this->db->queryObject($sql, [':id' => $id, ':id_session'=>$id_session], $this->getEntityClass());
    }
    public function getGoodsQuantity(){

        $tableName = $this->getTableName();
        $id_session = session_id();
        $sql = "SELECT count(*) as count FROM {$tableName} WHERE id_session = :id_session";

        return $this->db->queryOne($sql, [':id_session'=>$id_session], $this->getEntityClass());
    }
    public function update(DataEntity $entity){

        $params = [];
        $set = [];

        $params[':id'] = (int)$this->getId();
        $params[':id_session'] = session_id();
        $params[':id_product'] = $this->getIdProduct();
        $params[':quantity'] = $this->getQuantity();

        $sql = "UPDATE `carts` SET `id_product`= :id_product,`quantity`= :quantity WHERE id_cart=:id AND id_session = :id_session";
//        var_dump($sql, $params,$this->changes);

        $this->db->execute($sql, $params);
    }

    public function getEntityClass()
    {
        return Carts::class;
    }
}