<?php

namespace app\controllers;
use app\engine\Request;
use app\interfaces\IRender;
use app\interfaces\IAuthorization;

class defaultController extends Controller
{
    protected $defaultAction = 'View';

    /**
     * @return string
     */
    public function getDefaultAction(): string
    {
        return $this->defaultAction;
    }


    public function __construct(IRender $renderer, IAuthorization $autherizator)
    {
        parent::__construct($renderer, $autherizator);
    }
    public function actionView() {

        echo $this->render("index", []);
    }

}