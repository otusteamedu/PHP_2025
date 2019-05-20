<?php

namespace app\models;


class Users extends Model
{
    public $id_user;
    public $login;
    public $hash;

    public function getTableName()
    {
        return 'users';
    }

}