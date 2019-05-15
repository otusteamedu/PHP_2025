<?php

namespace app\models;

use app\interfaces\{Imodel};
use app\engine\{Db};


abstract class Model /*implements IModel*/
{
    protected $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    protected function getOne($id) {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName} WHERE id = {$id}";
        return $this->db->queryOne($sql);
    }

    protected function getAll() {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName}";
        return $this->db->queryAll($sql);
    }

    abstract public function getTableName();
}