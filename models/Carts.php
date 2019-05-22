<?php
/**
 * Created by IntelliJ IDEA.
 * User: iluka
 * Date: 22.05.2019
 * Time: 13:51
 */

namespace app\models;


class Carts
{
    protected $id_cart;
    protected $id_product;
    protected $id_user;
    protected $id_session;
    protected $quantity;

    public function __construct($id = null, $name = null, $description = null, $price = null)
    {
        parent::__construct();
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }

    public static function getTableName()
    {
        return 'cart';
    }

    public static function getId4Query()
    {
        return 'id_cart';
    }
}