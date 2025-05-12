<?php

namespace app\models\repositories;


use app\models\Repository;
use app\models\entities\DataEntity;
use app\models\entities\Carts;

class TestRepository extends Repository
{

    public function getTableName()
    {
        return 'test';
    }

    public function getAll(){

        $sql = "SELECT name FROM test";
        $params = [];
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
        $sql = "DELETE FROM {$tableName} WHERE id_cart = :id AND id_session = :id_session";
        $id = $entity->getId();
        $id_session = session_id();
//        var_dump('cartRepositoryDelete', $sql,$id,$id_session);
        $this->db->execute($sql, [":id" => $id,
                                  ":id_session" => $id_session]);
    }
    public function getCount($id){

        $tableName = $this->getTableName();
        $session_id = session_id();
        $sql = "SELECT quantity as count FROM $tableName WHERE id_product= :id AND id_session = :id_session";
//        var_dump($sql,$session_id,$id);
        return $this->db->queryAll($sql, [':id'=>$id, ':id_session'=>$session_id]);
    }
    public function getOne($id)
    {
        $tableName = $this->getTableName();
        $id_session = session_id();
        $sql = "SELECT * FROM {$tableName} WHERE id_product = :id AND id_session = :id_session";
        $res = $this->db->queryObject($sql, [':id' => $id, ':id_session'=>$id_session], $this->getEntityClass());
//        var_dump($res, $this->getEntityClass());die();
        return $res;
    }
    public function getGoodsQuantity(){
        $tableName = $this->getTableName();
        $id_session = session_id();
        $sql = "SELECT count(*) as count FROM {$tableName} WHERE id_session = :id_session";

        return $this->db->queryOne($sql, [':id_session'=>$id_session], $this->getEntityClass());
    }
//    public function update(DataEntity $entity){
//
//        $params = [];
//        $set = [];
//
//        $params[':id'] = (int)$this->getId();
//        $params[':id_session'] = session_id();
//        $params[':id_product'] = $this->getIdProduct();
//        $params[':quantity'] = $this->getQuantity();
//
//        $sql = "UPDATE `carts` SET `id_product`= :id_product,`quantity`= :quantity WHERE id_cart=:id AND id_session = :id_session";
////        var_dump($sql, $params,$this->changes);
//
//        $this->db->execute($sql, $params);
//    }
//    public function insert(DataEntity $entity)
//    {
//        //INSERT INTO `products`(`name`, `description`, `price`) VALUES (:name, :description, :price)
//        $tableName = $this->getTableName();
//
//        $params = [];
//        $columns = [];
//
//
//        $sql = "INSERT INTO 'carts' ('id_cart','id_product','id_user','id_session','quality') VALUES (':id_cart',':id_product',':id_user',':id_session',':quality')";
////        $params['id_cart'] = $entity->;
//
//        var_dump('INSERT',$sql, $params,$entity);die();
//        $this->db->execute($sql, $params);
//
//        $entity->setId($this->db->lastInsertId());
//    }
    public function getEntityClass()
    {
        return Carts::class;
    }

}
