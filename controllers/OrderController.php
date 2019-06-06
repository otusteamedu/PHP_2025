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
        $login =  $_SESSION['login'];
        session_regenerate_id(true);
//        $this->autherizator->login();
        $_SESSION['login'] = $login;
        header("Location: /");
    }
    public function actionView(){

        $orders =  App::call()->orderRepository->getAll();

        echo $this->render("orders", [
            'orders' => $orders
        ]);
    }
    public function actionSingle(){

        $order =  App::call()->orderRepository->getOne(App::call()->request->getParams()['id_order']);
//        var_dump($order);die();
        foreach ($order as &$item) {

            $item['img']=explode(',', $item['img']);
        }
        echo $this->render("orderContent", [
            'order' => $order,
            'id_order' => App::call()->request->getParams()['id_order']
        ]);
    }
}