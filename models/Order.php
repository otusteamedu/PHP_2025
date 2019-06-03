<?php
/**
 * Created by IntelliJ IDEA.
 * User: iluka
 * Date: 03.06.2019
 * Time: 12:47
 */

namespace app\models;

use app\controllers\Controller;
use app\interfaces\IAuthorization;
use app\interfaces\IRender;


class Order extends DbModel
{

    protected $id_order;
    protected $id_session;
    protected $status;
    protected $telefon;

    /**
     * Order constructor.
     * @param $id_order
     * @param $id_session
     * @param $status
     */
    public function __construct($id_order = null, $id_session = null, $status =null, $telefon = null)
    {
        $this->id_order = $id_order;
        $this->id_session = $id_session;
        $this->status = $status;
        $this->telefon = $telefon;
    }


    public static function  getOne($id){

        $sql = "SELECT id_product, `name_product`, `price`, `img`, `description` FROM products as p WHERE id_product = :id";
        $result =  Db::getInstance()->queryObject($sql, ['id' => $id], static::class);

        if(!$result){

            $error = 'Такого товара нет';
            throw new \Exception($error);
        }
        return $result;
    }
    public static function getTableName(){

        return 'orders';
    }
}