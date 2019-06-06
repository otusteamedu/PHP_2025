<?php

namespace app\models\repositories;


use app\models\entities\DataEntity;
use app\models\entities\Order;
use app\models\Repository;

class OrderRepository extends Repository
{
    public function  getAll(){

        $sql = "SELECT id_order, telefon, status FROM `orders`";
        $result =  $this->db->queryAll($sql, []);

        if(!$result){

            $error = 'Такого заказа нет';
            throw new \Exception($error);
        }
        return $result;
    }
    public function getOne($id)
    {
        $tableName = $this->getTableName();
        $sql = "SELECT p.id_product, name_product, price, description, img, quantity FROM `orders` as o, carts as c, products as p WHERE o.id_order=:id_order AND o.id_session=c.id_session AND c.id_product=p.id_product";

        return $this->db->queryAll($sql, [':id_order'=>$id]);
    }
    public function getTableName(){

        return 'orders';
    }

    public function getEntityClass()
    {
        return Order::class;
    }
}