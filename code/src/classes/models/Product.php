<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 4/20/2017
 * Time: 7:08 PM
 * Получить всю информацию  по продукту
 */

namespace classes\models;

use Classes\DataBase as connect;

class Product
{
    protected $product;

    protected $tables = 'product
                       inner join product_booking using(id_product)
                       inner join incoming using(id_incoming)
                       inner join producer using(id_producer)';

    protected $fields = array("product.id_product as id","concat(producer.name,' ', product.model) as tovar","incoming.price + product.price as cost",
        "warranty_period as warranty");


    public function get_product($id)
    {
        $stmt = new connect();
        $this->product = array_pop($stmt->select($this->tables,$this->fields,['id_booking'=> $id]));
        return $this->product;
    }

}