<?php

namespace app\controllers;


use app\engine\Request;
use app\Models\Carts;
use app\models\repositories\CartRepository;
use app\models\repositories\ProductsRepository;

class ApiController extends Controller
{
    public function actionDeleteFromCart(){

        $id_product = (new Request())->getParams()['id_product'];
        // нужно сделать проверку на равенство текущей сессии и сохранённой в корзине
       try{

           $entity = (new ProductsRepository())->getOne($id_product);
       }catch(\Exception $ex){


       }
        (new CartRepository())->delete($entity);

        header('Content-Type: application/json');
        echo json_encode(["deleted"=> "Ок",
                            "count" => 999]);
    }
    public function actionDecreaseQuantity(){


    }
}