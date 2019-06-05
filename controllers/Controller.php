<?php


namespace app\controllers;


use app\interfaces\IRender;
use app\models\entities\Carts;
use app\interfaces\IAuthorization;
use app\engine\Authorization;
use app\models\repositories\CartRepository;

abstract class Controller
{
    protected $action;
    protected $layout = 'main';
    protected $useLayout = true;
    private $renderer;
    protected $autherizator;

    public function __construct(IRender $renderer, IAuthorization $autherizator)
    {
//        var_dump($autherizator);
        $this->renderer = $renderer;
        $this->autherizator = $autherizator;
//        var_dump($this->autherizator );
    }

    public function render($template, $params = []) {

        $allow = false;
        $user = '';
        if ($this->autherizator->is_auth()) {
            $allow = true;
            $user = $this->autherizator->get_user();
        }
//        var_dump($this->autherizator );

        if ($this->useLayout) {
            return $this->renderTemplate("layouts/{$this->layout}",[
                'content' => $this->renderTemplate($template, $params),
                'menu' => [(new CartRepository())->getGoodsQuantity(), $allow, $user]
            ]);
        } else {
            return $this->renderTemplate($template, $params);
        }
    }
    public function renderTemplate($template, $params = []){

        return $this->renderer->renderTemplate($template, $params);
    }
    public function runAction($action = null, $data = null) {

        $this->action = $action ?: $this->getDefaultAction();
        $method = "action" . ucfirst($this->action);
//        var_dump('runAction',$this,$method,method_exists($this, $method),$this);
        if (method_exists($this, $method)) {

            $this->$method();
        }
        else {
            echo "404";
        }

    }

}