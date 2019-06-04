<?php

namespace app\controllers;

use app\engine\Request;
use app\interfaces\IRender;
use app\models\entities\Order;
use app\interfaces\IAuthorization;
use app\models\repositories\OrderRepository;


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

            (new OrderRepository)->insert($order);

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