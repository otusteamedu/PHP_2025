<?php

namespace classes\models;

use Classes\DataBase as connect;
class Customer
{
    protected $connect;
    protected $tables = 'Customer';
    protected $fields = ['id_customer as id' ,'name', 's_name','phone'];

    public function __construct()
    {
        $this->connect = new connect();
    }

    public function find_person($where)
    {
        $user = array_pop($this->connect->select($this->tables,$this->fields,$where));
        if(!is_null($user) ) return $user['id'];
            else return null;
    }

    public function new_customer(array $params)
    {
       $id = $this->connect->insert($this->tables,$params);
       if(!is_null($id)) return $id;
       else echo "Произошла ошибка";
    }

}