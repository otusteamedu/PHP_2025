<?php

namespace app\controllers;


use app\engine\Request;
use app\Models\Carts;

class ApiController extends Controller
{
    public function actionDeleteFromCart(){

        $request = (new Request())->getParams()['id_cart'];

        Carts::delete($request);

        header('Content-Type: application/json');
        echo json_encode(['deleted'=> $request]);
    }
    public function actionDecreaseQuantity(){


    }
}