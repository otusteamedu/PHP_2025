<?php

namespace MyTestApp\Commands\Redis;

Class CommandClear {

    public $connect;

    public function __construct() {
        $this->connect = (new \MyTestApp\Commands\Redis\Connect)->connect;
    }

    public function clear() {
        $this->connect->flushDB();
        return "База очищена";
    }
    
}