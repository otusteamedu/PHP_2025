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

            if ($key == "db" || $key == "id" || $key == 'changes') continue;
            $params[":{$key}"] = $value;
            $columns[] = "`$key`";
        }

        $columns = implode(", ", $columns);
        $value = implode(", ", array_keys($params));

        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$value})";
//        var_dump($sql, $params);
        Db::getInstance()->execute($sql, $params);

        $this->id = Db::getInstance()->lastInsertId();
    }

    public static function delete($id)
    {
        $tableName = static::getTableName();
        $sql = "DELETE FROM {$tableName} WHERE id = :id";
        Db::getInstance()->execute($sql, ["id" => $id]);
    }
    public function update(){

        $params = [];
        $set = [];

        $params[':id'] = (int)$this->getId();
        foreach($this->changes as $el){

            $set[] = $el . "= :" . $el;
            $params[':'. $el] = $this->$el;
        }
        $set = implode(", ", $set);

        $sql = "UPDATE `products` SET {$set} WHERE id_product=:id";
        Db::getInstance()->execute($sql, $params);
    }

    public function save() {

        if (is_null($this->getId()))
            $this->insert();
        else
            $this->update();
    }
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {

            $this->$property = $value;

            if(!in_array($property,$this->changes)){

                array_push($this->changes,$property);
            }
        }
    }


    abstract static public function getTableName();
}