<?php


namespace app\controllers;
use app\models\Carts;
use app\interfaces\IRender;

class CartController extends Controller
{
    public function __construct(IRender $renderer)
    {
        parent::__construct($renderer);
    }
    public function actionView() {

        $cart =  Carts::getAll();
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
        $id =$_GET['id'];
        $session_id = session_id();
        $cart= new Carts(
            null,
            $id,
            null,
            $session_id,
            1.
        );
//        var_dump($cart);die("Carts");
        $cart->insert();
        header("location: /?c=product&a=catalog");
    }
}