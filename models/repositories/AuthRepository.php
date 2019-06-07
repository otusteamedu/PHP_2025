<?php

namespace app\models\repositories;


use app\engine\App;
use app\models\entities\Users;
use app\models\Repository;

class AuthRepository extends Repository
{

    public function getObject($login)
    {
        $sql = "SELECT * FROM users WHERE login = :login";
//        var_dump($sql,[':login' => $login]);

        return $this->db->queryObject($sql, [':login' => $login], $this->getEntityClass());
    }

    public function getTableName()
    {
        return 'users';
    }
    public function getEntityClass()
    {
        return Users::class;
    }
}