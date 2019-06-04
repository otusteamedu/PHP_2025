<?php

namespace app\models\repositories;


use app\models\entities\Products;
use app\models\Repository;
use app\models\entities\DataEntity;

class ProductsRepository extends Repository
{
    public function getEntityClass(){

        return Products::class;
    }

    public function getTableName()
    {
        return 'products';
    }
    public function getAll(){

        $sql = "SELECT id_product, name_product, `price`, `img`, `description` FROM products as p, units as u, product_category as c, product_types as t WHERE p.id_unit=u.id_unit AND p.id_product_category = c.id_product_category AND p.id_product_type = t.id_product_type";
        $result = $this->db->queryAll($sql);

        if(count($result)==0){

            $error = 'В каталоге нет товаров';
            throw new \Exception($error);
        };
        return $result;
    }
    public function getOne($id){

        $sql = "SELECT id_product, `name_product`, `price`, `img`, `description` FROM products as p WHERE id_product = :id";
        $result =  $this->db->queryObject($sql, ['id' => $id], $this->getEntityClass());
        var_dump($result,$this->getEntityClass());
        if(!$result){

            $error = 'Такого товара нет';
            throw new \Exception($error);
        }
        return $result;
    }
    public function update(DataEntity $entity){

        $params = [];
        $set = [];

        $params[':id'] = (int)$entity->getId();
        foreach($entity->changes as $el){

            $set[] = $el . "= :" . $el;
            $params[':'. $el] = $this->$el;
        }
        $set = implode(", ", $set);

        $sql = "UPDATE `products` SET {$set} WHERE id_product=:id";
        $this->db->execute($sql, $params);
    }
}