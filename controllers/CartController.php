<?php


namespace app\controllers;
use app\models\Carts;
use app\interfaces\IRender;
use app\engine\Request;
use app\interfaces\IAuthorization;
use app\models\repositories\CartRepository;

class CartController extends Controller
{
    public function __construct(IRender $renderer, IAuthorization $autherizator)
    {
        parent::__construct($renderer, $autherizator);
    }
    public function actionView() {

        $cart =  (new CartRepository())->getAll();

        foreach ($cart as &$item) {

            $item['img']=explode(',', $item['img']);
        }
//        var_dump();
        echo $this->render("cart", [
            'cart' => $cart,
            'img_small' => IMG_SMALL
        ]);
    }
    public function actionAdd(){

//        var_dump('actionAdd',$_GET);
        $id =(new Request())->getParams()['id'];//$_GET['id'];
        $session_id = session_id();

        $count = (new CartRepository())->getCount($id)[0]['count'];
        $cart=null;
        if ($count == 0){

            $cart= new Carts(
                null,
                $id,
                null,
                $session_id,
                1.
            );
            $cart->insert();
        }else{

            $cart = (new CartRepository())->getOne($id);
            $cart->setQuantity($cart->getQuantity()+1);
            $cart->update();
//            var_dump($cart);
        }
//        $cart->save();
//        var_dump($cart);

        header("location: /product/catalog");
    }
    public function actionDelete(){


    }
}