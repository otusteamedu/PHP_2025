<?php
namespace App;
class Route{

    public static function route()
    {
        $params = $_REQUEST;
        $method = $_SERVER['REQUEST_METHOD'];


         switch ($method){
            case 'POST':
                $str = $params['string']; 
       return response( checkBrackets($str), "Строка со скобками корректна.\n");
            default:  
       return response( false,"Ожидается POST-запрос с параметром 'string'.\n");
         }


    }
}