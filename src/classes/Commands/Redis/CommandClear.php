<?php

namespace MyTestApp\Commands\Redis;

Class CommandClear extends Connect {

    public function clear() {
        $this->connect->flushDB();
        return "База очищена";
    }
    
}