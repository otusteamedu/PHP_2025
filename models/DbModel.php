<?php

namespace app\models;

use app\engine\Db;
use app\interfaces\IModel;

abstract class DbModel extends Models implements IModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public static function getOne($id)
    {
        $tableName = static::getTableName();
        $id_db = static::getId4Query();
//        var_dump('tableName', $tableName);
        $sql = "SELECT * FROM {$tableName} WHERE {$id_db} = :id";
//        var_dump('getOne',$sql);
        return Db::getInstance()->queryObject($sql, ['id' => $id], static::class);
    }

    public static function getAll()
    {
        $tableName = static::getTableName();
        $columns = static::$columns;
        $condition=static::$condition;
//        $params=static::$params;
        $params=static::getParams();

        $sql = "SELECT $columns FROM {$tableName} WHERE $condition";
//        var_dump($sql, $params, $columns);
        return Db::getInstance()->queryAll($sql, $params);
    }

    public function insert()
    {
        //INSERT INTO `products`(`name`, `description`, `price`) VALUES (:name, :description, :price)
        $tableName = static::getInsertTableName();
        $params = $this->getInsertParams();
        $values = $this->getValues();
        $columns = $this->getColumns();
//        var_dump($params,$values,$columns);

        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$values})";
var_dump($sql,$params);
        Db::getInstance()->execute($sql, $params);

        $this->setId(Db::getInstance()->lastInsertId());
    }

    public function delete()
    {
        $tableName = static::getTableName();
        $sql = "DELETE FROM {$tableName} WHERE id = :id";
        $id = $this->getId();
        Db::getInstance()->execute($sql, ["id" => $id]);

    }

    public function update()
    {
        $set = $this->getUpdateSet();
        $condition = $this->getUpdateCondition();
        $sql = "UPDATE `products` SET $set WHERE $condition";
        var_dump($sql);
        Db::getInstance()->execute($sql, []);
    }

    public function save() {

    }

    abstract static public function getTableName();
}