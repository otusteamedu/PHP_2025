<?php

namespace app\Models;
use  app\models\{Products};

class Cart extends Model{

    private $content = [/*id=>[Product, quantity]*/]; // ассоциативный массив где ключ это некий id, а значение это массив с товаром и количеством

    public function __construct()
    {
        $data=$this->getAll();
        $this->content[$data['id_cart']]= new Products();
    }
    function readCart($session){

        $result = [];
        $sql = "SELECT id, product_name, quantity, price*quantity as total, description, price, img FROM cart, enginodb.catalog where id_session = '$session' and id_product = id";
        $result = getAssocResult($sql);
//    var_dump($result);
        return $result;
    }
    public function add(Product $product, $quantity){

        // добавляет позицию в корзину
    }
    function removeItem($id, $session){

        $id=(int)$id;
        $count_query = "SELECT quantity as count FROM `cart` where id_product= $id and id_session='$session'";
        $count = getAssocResult($count_query)[0]['count'];
//    var_dump($id,$count_query,$count);
        $sql= '';
        if($count>1){

            $sql = "UPDATE `cart` SET `quantity`=quantity-1 WHERE id_session = '{$session}' and id_product = {$id}";
//        var_dump($id,$count_query,$count,$sql); die();
        }elseif($count == 1){

            $sql = "DELETE FROM `cart` WHERE id_session = '$session' and id_product = $id";
        }
//var_dump($sql);
        return isset($sql)?executeQuery($sql):false;
    }
    function addItem($id, $session){

        $id=(int)$id;
        $count_query = "SELECT count(*) as count FROM `cart` where id_product= $id";
//    var_dump($id,$count_quary);
        $count = getAssocResult($count_query)[0]['count'];
//    var_dump($count);die();
        if(!$count){

            $sql = "INSERT INTO `cart`( `id_product`, `id_session`, quantity) VALUES ($id, '$session', 1);";
        }else{

            $sql = "UPDATE `cart` SET `quantity`=quantity+1 WHERE id_session = '{$session}' and id_product = {$id}";
        }
//    var_dump($sql);
        return executeQuery($sql);

    }
    private function findIndexOf(Product $product){

        // находит индекс определённого товара в корзине
    }

    public function getTableName()
    {
        return 'cart';
    }
}