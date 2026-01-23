<?php
namespace classes\models;

use Classes\DataBase as connect;

class Delivery
{
    protected $connect;
    protected static $tables = 'delivery';
    protected static $fields = array('name','phone','address','completed','notes');


    public  function __construct()
    {
        $this->connect = new connect();
    }

    public function get_data($where)
    {
        $result = $this->connect->select($this::$tables,$this::$fields,$where);
        var_dump($result);
    }

    public function new_order($data)
    {
        $result =  $this->connect->insert($this::$tables,$data);
        return $result;
    }

}