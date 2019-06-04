<?php

namespace app\models;


use app\models\entities\DataEntity;

class Users extends DataEntity
{
    public $id_user;
    public $login;
    public $password;

    public function getId(){

        return $this->id_user;
    }
    public function getIdName(){

        return 'id_user';
    }
    public function setId($value){

        $this->id_user = $value;
    }
}