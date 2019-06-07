<?php

namespace app\models\repositories;

use app\engine\App;
use app\models\entities\Users;
use app\models\Repository;

class UsersRepository extends Repository
{

    public function getObject($login)
    {
        $sql = "SELECT * FROM users WHERE login = :login";
//        var_dump($sql,[':login' => $login]);

        return $this->db->queryObject($sql, [':login' => $login], $this->getEntityClass());
    }
    public function getUserByHash($hash)
    {
        $sql = "SELECT * FROM users WHERE hash = :hash";
//var_dump($sql,$hash);
        return $this->db->queryObject($sql, [':hash' => $hash], $this->getEntityClass());
    }
    public function getOne($id)
    {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName} WHERE id_user = :id";
//var_dump($sql, ['id' => $id]);
        return $this->db->queryObject($sql, ['id' => $id], $this->getEntityClass());
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