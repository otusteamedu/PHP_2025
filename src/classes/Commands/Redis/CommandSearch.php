<?php

namespace MyTestApp\Commands\Redis;

Class CommandSearch {

    public $connect;

    public function __construct() {
        $this->connect = (new \MyTestApp\Commands\Redis\Connect)->connect;
    }

    public function search($json_string) {

        $array = json_decode($json_string,true);
        $get_params_array = $array["params"];


        $param_array = $match_array = [];
        $iterator = null;
        while ($keys = $this->connect->scan($iterator)) {
            foreach ($keys as $key) {

                $value = $this->connect->hgetall($key);
                $priority = null;

                foreach($value AS $k=>$res) {
                    if($k !== "priority")
                        $param_array[$k] = $res;
                    else 
                        $priority = $res;
                }

                if($get_params_array == $param_array) {
                    $match_array[$key] = $priority;
                }
    
            }
        }

        arsort($match_array);

        $answer = "<h2>Поиск по базе</h2>";

        foreach($match_array AS $key=>$priority) {
            $answer .= "Cобытие {$key}<br/>";
            $value = $this->connect->hgetall($key);
            foreach($value AS $k=>$res)
                $answer .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;    $k: {$res}</p>";
            break;
            
        }

        return $answer;

    }
}