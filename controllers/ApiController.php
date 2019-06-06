<?php

namespace app\controllers;


use app\engine\App;
use app\engine\Request;
//use app\Models\entities\Carts;
use app\models\repositories\CartRepository;
use app\models\repositories\ProductsRepository;

class ApiController extends Controller
{
    public function actionDeleteFromCart(){

        $id_product = App::call()->request->getParams()['id_product'];
        $id_cart = App::call()->request->getParams()['id_cart'];
        // нужно сделать проверку на равенство текущей сессии и сохранённой в корзине
       try{

           $entity = App::call()->cartRepository->getOne($id_product);
       }catch(\Exception $ex){

       }
       $count = App::call()->cartRepository->getCount($id_product)[0]['count'];
       $cart = App::call()->cartRepository->getOne($id_product);
       $count--;
//       var_dump('COUNT',$count);
       if($count === 0){

           App::call()->cartRepository->delete($cart);
//           var_dump('delete');

       }else{

           $cart->setQuantity($count);
           App::call()->cartRepository->save($cart);
//           var_dump('cart',$cart, $cart->getChanges());
       }
//        var_dump((new ProductsRepository())->getAll());

        header('Content-Type: application/json');
        echo json_encode(["deleted" => $id_cart, "count" => $count]);
    }
    public function actionsetOrderStatus(){


        $newstatus = App::call()->orderRepository->changeStatus();
//var_dump(json_encode(['newstatus' => $newstatus]),JSON_UNESCAPED_UNICODE);

        return json_encode(['newstatus' => 'dfsfsf'], JSON_UNESCAPED_UNICODE);
    }
}