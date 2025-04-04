<?php

namespace MyTestApp\Methods\Redis;

Class MethodClear extends Method {

    public function clear() {
        $this->connect->flushDB();
        return "База очищена";
    }
    
}