<?php
namespace App;

class App
{
    private static $controllerName;

    public static function start()
    {
        $url = isset($_GET['url']) ? trim($_GET['url']) : APP_DEFAULT_CONTROLLER;
        $urlParts = explode('/', rtrim($url, '/'));

        $actionParams = array_slice($urlParts,2);

        self::$controllerName = $urlParts[0];

        // -- в оригинале был этот код, но у меня он не работал Class not found
        $controllerName =  "App\\Controllers\\" .self::$controllerName . 'Controller';
        $controller = new  $controllerName();

        $actionName = isset($urlParts[1]) ? $urlParts[1] . 'Action' : 'indexAction';

        if(method_exists($controller, $actionName ))
        {
            call_user_func_array(array($controller,$actionName),$actionParams);
        }else
        {
            echo "Произошла ошибка вызова контроллера";
        }
    }

    /**
     * Возвращаем имя текущего контроллера
     */
    public static function getCurrentController()
    {
        return strtolower(self::$controllerName);
    }
}