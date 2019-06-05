<?php

namespace app\models;
use app\engine\Db;
use app\models\entities\DataEntity;
use app\models\entities\Carts;

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

        foreach ($entity->properties as $value) {

            $params[":{$value}"] = $entity->$value;
            $columns[] = "`$value`";
        }

        $columns = implode(", ", $columns);
        $value = implode(", ", array_keys($params));

        $sql = "INSERT INTO {$tableName} ({$columns}) VALUES ({$value})";
//        var_dump('INSERT',$sql, $params,$entity);
        $this->db->execute($sql, $params);

        $entity->setId($this->db->lastInsertId());
    }

    public function delete(DataEntity $entity)
    {
        $tableName = $this->getTableName();
        $id_name = $entity->getIdName();

        $sql = "DELETE FROM {$tableName} WHERE $id_name = :id";

        $this->db->execute($sql, ["id" => $entity->getId()]);
    }
    public function update(DataEntity $entity){

        $params = [];
        $set = [];

        $params[':id'] = (int)$entity->getId();
//        var_dump($entity->getChanges()); die();
        foreach($entity->getChanges() as $key => $el){

            $set[] = $key . "= :" . $key;
            $params[':'. $key] = $el;
        }
        $set = implode(", ", $set);

        $tableName = $entity->getTableName();
        $id_name = $entity->getIdName();

        $sql = "UPDATE $tableName SET {$set} WHERE $id_name = :id";
//var_dump('update',$sql, $params);die();
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