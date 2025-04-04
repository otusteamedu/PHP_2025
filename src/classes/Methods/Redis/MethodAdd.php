<?php

namespace MyTestApp\Methods\Redis;

Class MethodAdd extends Method {

    public function add($json_string) {

        $answer = "<h2>Добавление записи</h2>";

        $array = json_decode($json_string,true);
        $this->connect->hset($array["event"], 'priority', $array['priority']);
        foreach($array['conditions'] AS $key=>$val) {
            $this->connect->hset($array["event"], $key, $val);
        }

        $answer .= "<p>Запись {$json_string} добавлена</p>";

        return $answer;

    }
    
}