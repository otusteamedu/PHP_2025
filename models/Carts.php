<?php
/**
 * Created by IntelliJ IDEA.
 * User: iluka
 * Date: 22.05.2019
 * Time: 13:51
 */

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
//    public static $condition = "c.id_product=p.id_product AND id_session= :id AND ctg.id_product_category=p.id_product_category AND u.id_unit=p.id_unit AND t.id_product_type=p.id_product_type";
////    public static $params = ['id'=>session_id()];
//    public static $columns = "id_cart,p.id_product,name_product,price,img,description, category, name_unit, type, quantity";

    public function __construct($id_cart,$id_product = null, $id_user = null, $id_session = null, $quantity = null)
    {
        parent::__construct();
        $this->id_cart = $id_cart;
        $this->id_product = $id_product;
        $this->id_user = $id_user;
        $this->id_session = $id_session;
        $this->quantity = $quantity;
//        var_dump('Cart',$this, $quantity);
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
    public static function getInsertTableName()
    {
        return 'carts';
    }

    public static function getId4Query()
    {
        return 'id_cart';
    }
    public function getValues(){

        return ":id_product, :id_user, :id_session,:quantity";
    }
    public function getColumns(){

        return "id_product, id_user, id_session,quantity";
    }
//    public static function getInsertParams(){
//
//        return ['id'=>session_id()];
//    }
    public static function getParams(){

        return ['id'=>session_id()];
    }
    public function getInsertParams(){

        return ['id_product'=>$this->id_product, 'id_user'=>$this->id_user, 'id_session'=>$this->id_session,'quantity'=>$this->quantity];
    }
    public function setId($value){

        $this->id_cart = $value;
    }
}