<?php

namespace app\controllers;

use app\engine\App;
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

        $telefon = App::call()->request->getParams()['approve']? App::call()->request->getParams()['telefon'] : null;

        if($telefon){

            $order = new Order(null, session_id(), 'Обрабатывается', $telefon);
//        try{

            App::call()->orderRepository->insert($order);

//        }catch (\Exception $ex){
//
//            echo $ex->getMessage();
//        }
        }
        session_regenerate_id(true);
//        $this->autherizator->login();
        header("Location: /");
    }
    public function actionView(){


        echo $this->render("orderContent", []);
    }
}