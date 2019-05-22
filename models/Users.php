<?php

namespace app\models;


class Users extends DbModel
{
    public $id;
    public $login;
    public $pass;

    public static function getTableName()
    {
        return 'users';
    }

}