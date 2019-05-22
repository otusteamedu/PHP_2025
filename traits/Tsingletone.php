<?php

namespace app\traits;


trait Tsingletone
{
    private static $instance = null;

    private function __construct()   {  }
    private function __clone()   {  }
    private function __wakeup()   {  }


    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return self::$instance;
    }

}