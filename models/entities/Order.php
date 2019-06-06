<?php

namespace app\models\entities;

use app\controllers\Controller;
use app\interfaces\IAuthorization;
use app\interfaces\IRender;
use app\models\entities\DataEntity;


class Order extends DataEntity
{

    protected $id_order;
    protected $id_session;
    protected $status;
    protected $telefon;
    public $properties = ['id_session','status', 'telefon'];

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
    public function getId(){

        return $this->id_order;
    }
    public function getIdName(){

        return 'id_order';
    }
    public function setId($value){

        $this->id_order = $value;
    }
    public function getTableName(){

        return 'orders';
    }

    /**
     * @param null $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
        if(!in_array($status, $this->changes)){

            $this->changes['status'] = $status;
//          var_dump($this->changes); die();
        }
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }

}