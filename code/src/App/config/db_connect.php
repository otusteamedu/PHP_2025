<?php
namespace App\config;
use PDO;

class db_connect extends  PDO
{
    protected static $instance;

    const HOST = '127.0.0.1';
    const DB_NAME = '';
    const USER = '';
    const PASSWORD = '';
    const CHARSET = 'UTF8';
    const PORT = '3306';
    const DB_DSN_MYSQL = 'mysql:host =' .self::HOST.';port ='.self::PORT.';dbname='. self::DB_NAME .';charset ='.self::CHARSET;


    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new self(self::DB_DSN_MYSQL,self::USER,self::PASSWORD);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }



}