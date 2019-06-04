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
}