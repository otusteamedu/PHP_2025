<?php

namespace app\controllers;

use app\engine\Request;
use app\interfaces\IRender;
use app\models\Order;
use app\models\Products;
use app\interfaces\IAuthorization;


class OrderController extends Controller
{

    public function __construct(IRender $renderer, IAuthorization $autherizator)
    {
        parent::__construct($renderer, $autherizator);
    }

    public function actionPrepear(){


        echo $this->render("order", []);
    }
    public function actionDo(){

        $telefon = (new Request())->getParams()['approve']? (new Request())->getParams()['telefon'] : null;

        if($telefon){

            $order = new Order(null, session_id(), 'Обрабатывается', $telefon);
//        try{

            $order->insert();

//        }catch (\Exception $ex){
//
//            echo $ex->getMessage();
//        }
            session_destroy();
            header("Location: /");
        }
    }
    public function actionView(){


        echo $this->render("orderContent", []);
    }
}