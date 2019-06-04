<?php

namespace app\models;


use app\models\entities\DataEntity;

class Users extends DataEntity
{
    public $id;
    public $login;
    public $pass;

}