<?php

namespace MyTestApp\Commands\Redis;

Class CommandShow {

    public $connect;

    public function __construct() {
        $this->connect = (new \MyTestApp\Commands\Redis\Connect)->connect;
    }

    public function show() {

        $iterator = null;
        $answer = "<h2>Записи в базе</h2>";
        
        while ($keys = $this->connect->scan($iterator)) {

            foreach ($keys as $key) {

                $answer .= "<p><b>{$key}</b></p>";
                $value = $this->connect->hgetall($key);
                foreach($value AS $k=>$res)
                    $answer .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;    $k: {$res}</p>";

            }

        }

        return $answer;

    }

}