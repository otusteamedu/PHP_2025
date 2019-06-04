<?php

namespace app\models;
use app\engine\Db;
use app\models\entities\DataEntity;

abstract class Repository
{
    protected $db;

    /**
     * Repository constructor.
     * @param $db
     */
    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function getOne($id)
    {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName} WHERE id = :id";

        return $this->db->queryObject($sql, ['id' => $id], $this->getEntityClass());
    }

    public function getAll()
    {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM {$tableName}";
        return $this->db->queryAll($sql);
    }

    public function insert(DataEntity $entity)
    {
        //INSERT INTO `products`(`name`, `description`, `price`) VALUES (:name, :description, :price)
        $tableName = $this->getTableName();

        $params = [];
        $columns = [];

        foreach ($entity as $key => $value) {

            if ($key == "db" || $key == "id" || $key == 'changes') continue;
            $params[":{$key}"] = $value;
            $columns[] = "`$key`";
        }

        $columns = implode(", ", $columns);
        $value = implode(", ", array_keys($params));

        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$value})";
//        var_dump($sql, $params);
        $this->db->execute($sql, $params);

        $entity->id = $this->db->lastInsertId();
    }

    public function delete(DataEntity $entity)
    {
        $tableName = $this->getTableName();
        $sql = "DELETE FROM {$tableName} WHERE id = :id";
        $this->db->execute($sql, ["id" => $entity->id]);
    }
    public function update(DataEntity $entity){

        $params = [];
        $set = [];

        $params[':id'] = (int)$entity->getId();
        foreach($entity->changes as $el){

            $set[] = $el . "= :" . $el;
            $params[':'. $el] = $this->$el;
        }
        $set = implode(", ", $set);
        $tableName = $this->getTableName();
        $id = $this->getIdName();
        $sql = "UPDATE $tableName SET {$set} WHERE $id=:id";

        $this->db->execute($sql, $params);
    }

    public function save(DataEntity $entity) {

        if (is_null($entity->getId()))

            $this->insert($entity);
        else

            $this->update($entity);
    }
    abstract public function getEntityClass();
    abstract public function getTableName();
}