<?php
namespace Classes;

class View
{
    public function __construct()
    {
        //Add ob_start to send  headers from views
        ob_start();
    }

    /**
    * Шаблон который нужно отобразить
    */
    public function render($name, array $data = array()){
        require APP_BASE_PATH . '/resource/view/header.php';
        $this->renderPartial($name,$data);
        require APP_BASE_PATH.'/resource/view/footer.html';
    }

    public function renderPartial($name, array $data = array()){
        if(!empty($data)){
            extract($data);
        }
        require (APP_BASE_PATH.'/resource/view/' .$name. '.php');
    }

    public function __get($name){
        return "";
    }
}