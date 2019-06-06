<?php

namespace app\models\repositories;


use app\models\entities\DataEntity;
use app\models\entities\Order;
use app\models\Repository;

class OrderRepository extends Repository
{
    public function  getOne($id){

        $sql = "SELECT id_product, `name_product`, `price`, `img`, `description` FROM products as p WHERE id_product = :id";
        $result =  $this->db->queryObject($sql, ['id' => $id], $this->getEntityClass());

        if(!$result){

            $error = 'Такого товара нет';
            throw new \Exception($error);
        }
        return $result;
    }
    public function getTableName(){

        return 'orders';
    }

    public function getEntityClass()
    {
        return Order::class;
    }
}