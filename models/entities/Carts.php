<?php

namespace app\models;
use app\controllers\Controller;
use app\models\DbModel;
use app\engine\Db;
use app\models\entities\DataEntity;

class Carts extends DataEntity
{
    protected $id_cart;
    protected $id_product;
    protected $id_user;
    protected $id_session;
    protected $quantity;
    protected $changes=[];

    public function __construct($id_cart=null,$id_product = null, $id_user = null, $id_session = null, $quantity = null)
    {
        parent::__construct();
        $this->id_cart = $id_cart;
        $this->id_product = $id_product;
        $this->id_user = $id_user;
        $this->id_session = $id_session;
        $this->quantity = $quantity;
    }

    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
}