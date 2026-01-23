<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 3/11/2017
 * Time: 10:33 PM
 */

namespace App\Controllers;

use Classes\DataBase;
use Classes\View;
use Classes\session;

abstract class Controller
{
    /**
     * @var View
     */

    protected $view;
    protected $session;

    public function __construct()
    {
        $this->view = new View();
        $this->session = new session();
    }

        public function isAjax(){
        return (isset($_GET['ajax'])||(isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }

    public  function indexAction(){
        echo __CLASS__. '->' .__METHOD__ . '<br/>';
    }

    /**
     * перенаправление  пользователя на указанный url
     * @param $url
     */
    public function redirect($url){
        header ('Location: '. $url);
        die;
    }

    /**
     * Возвращает url для указанных параметров
     * Число параметров - не менее одного
     * Первый - имя контроллера
     * Остальные в порядке использования - имя метода, значения параметров
     * @param $controller
     * @return mixed
     */

    public static function url($controller){
        $args = func_get_args();

        return APP_BASE_URL .implode("/",$args);
    }

    public function isPost(){
        return !empty($_POST);
    }

    public function bd()
    {
        try{
            return new DataBase();
        }catch(Exception $e){
            $e->getMessage();
        }
    }
}