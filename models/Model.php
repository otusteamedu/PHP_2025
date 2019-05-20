<?php

namespace app\models;

use app\interfaces\IModel;
use app\engine\Db;

abstract class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function getOne($id) {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName} WHERE id = :id";
        return $this->db->queryOne($sql, ['id'=>$id]);
    }

    public function getAll() {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName}";
        return $this->db->queryAll($sql);
    }

//    public function insert() {
//
//    }
//
//    public function delete($product) {
//
//        $tableName = $this->getTableName();
//        $sql = "delete FROM {$tableName} WHERE id_product = :id";
//        var_dump($sql);
//        $this->db->execute($sql, ['id'=>(int)$product->id_product]);
//    }
//
//    public function update() {
//
//    }

    abstract public function getTableName();
}