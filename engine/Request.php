<?php


namespace app\engine;


class Request
{
    private $requestString;
    private $controllerName;
    private $actionName;
    private $method;
    private $params;

    /**
     * Request constructor.
     * @param $controllerName
     */
    public function __construct()
    {
        $this->requestString = $_SERVER['REQUEST_URI'];
//        var_dump($_REQUEST);
        $this->parseRequest();
    }
    private function parseRequest(){

        $this->method = $_SERVER['REQUEST_METHOD'];
        $url = explode('/', $this->requestString);
//        var_dump($_REQUEST['REQUEST_URI'],$url);
        $this->controllerName = $url[1];
        $this->actionName = $url[2];
        $this->params = $_REQUEST;
//        var_dump('REQUESTCLASS',$_REQUEST);
    }

    /**
     * @return mixed
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @return mixed
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

}