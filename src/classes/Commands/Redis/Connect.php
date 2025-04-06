<?php

namespace MyTestApp\Commands\Redis;

Class Connect {

    public $connect;

    public function __construct() {
        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), 6379);
        $this->connect = $redis;
    }

}