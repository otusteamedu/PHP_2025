<?php

namespace MyTestApp\Methods\Redis;

Class MethodSearch extends Method {

    public function search() {

        $array = json_decode($_POST["search"],true);
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

        foreach($match_array AS $key=>$priority) {
            $this->answer .= "Cобытие {$key}<br/>";
            $value = $this->connect->hgetall($key);
            foreach($value AS $k=>$res)
                $this->answer .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;    $k: {$res}</p>";
            break;
            
        }
    }
}