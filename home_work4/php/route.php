<?php
require 'validate.php';
require 'response.php';
class Route{

    public static function route()
    {
        $params = $_REQUEST;
        $method = $_SERVER['REQUEST_METHOD'];


         switch ($method){
            case 'POST':
                $str = $params['string']; 
        response( checkBrackets($str), "Строка со скобками корректна.\n");

                break;
            default:  
        response( false,"Ожидается POST-запрос с параметром 'string'.\n");
         }


    }
}