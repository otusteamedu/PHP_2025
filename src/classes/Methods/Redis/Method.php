<?php

namespace MyTestApp\Methods\Redis;

Class Method {

    public $connect;
    public $answer = "";

    public function __construct() {
        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), 6379);
        $this->connect = $redis;
    }

}