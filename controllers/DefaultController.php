<?php
/**
 * Created by IntelliJ IDEA.
 * User: iluka
 * Date: 27.05.2019
 * Time: 9:26
 */

namespace app\controllers;
use app\engine\Request;
use app\interfaces\IRender;
use app\interfaces\IAuthorization;

class defaultController extends Controller
{
    public function __construct(IRender $renderer, IAuthorization $autherizator)
    {
//        var_dump($autherizator);
        parent::__construct($renderer, $autherizator);
    }
    public function actionView() {

        echo $this->render("index", []);
    }

}