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
        $sql = "SELECT * FROM {$tableName} WHERE id = :id";
        return Db::getInstance()->queryObject($sql, ['id' => $id], static::class);
    }

    public static function getAll()
    {
        $tableName = static::getTableName();
        $sql = "SELECT * FROM {$tableName}";
        return Db::getInstance()->queryAll($sql);
    }

    public function insert()
    {
        //INSERT INTO `products`(`name`, `description`, `price`) VALUES (:name, :description, :price)
        $tableName = static::getTableName();

        $params = [];
        $columns = [];

        foreach ($this as $key => $value) {

            if ($key == "db" || $key == "id") continue;
            $params[":{$key}"] = $value;
            $columns[] = "`$key`";

        }

        $columns = implode(", ", $columns);
        $value = implode(", ", array_keys($params));

        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$value})";

//        var_dump($sql, $params);die();

        Db::getInstance()->execute($sql, $params);

        $this->id = Db::getInstance()->lastInsertId();
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

    }

    public function save() {
        if (is_null($this->id))
            $this->insert();
        else
            $this->update();
    }

    abstract static public function getTableName();
}