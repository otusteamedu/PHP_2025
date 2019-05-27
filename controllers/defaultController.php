<?php
/**
 * Created by IntelliJ IDEA.
 * User: iluka
 * Date: 27.05.2019
 * Time: 9:26
 */

namespace app\controllers;
use app\interfaces\IRender;

class defaultController extends Controller
{
    public function __construct(IRender $renderer)
    {
        parent::__construct($renderer);
    }
    public function actionView() {

        echo $this->render("index" . ".tmpl", []);
    }

}