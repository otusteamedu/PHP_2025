<?php

namespace app\controllers;


use app\engine\Request;
//use app\Models\entities\Carts;
use app\models\repositories\CartRepository;
use app\models\repositories\ProductsRepository;

class ApiController extends Controller
{
    public function actionDeleteFromCart(){

        $id_product = (new Request())->getParams()['id_product'];
        $id_cart = (new Request())->getParams()['id_cart'];
        // нужно сделать проверку на равенство текущей сессии и сохранённой в корзине
       try{

           $entity = (new ProductsRepository())->getOne($id_product);
       }catch(\Exception $ex){

       }
       $count = (new CartRepository())->getCount($id_product)[0]['count'];
       $cart = (new CartRepository())->getOne($id_product);
       $count--;
//       var_dump('COUNT',$count);
       if($count === 0){

           (new CartRepository())->delete($cart);
//           var_dump('delete');

       }else{

           $cart->setQuantity($count);
           (new CartRepository())->update($cart);
//           var_dump('cart',$cart, $cart->getChanges());
       }
//        var_dump((new ProductsRepository())->getAll());

        header('Content-Type: application/json');
        echo json_encode(["deleted" => $id_cart, "count" => $count]);
    }
    public function actionDecreaseQuantity(){


    }
}