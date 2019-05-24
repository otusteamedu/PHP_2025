<?php


namespace app\controllers;
use app\models\Carts;


class CartController extends Controller
{
    protected $action;
    protected $layout = 'main';
    protected $useLayout = true;
    protected $defaultAction = 'view';

    public function actionView() {

        $cart =  Carts::getAll();
//        var_dump($cart);

        echo $this->render("cart", [
            'cart' => $cart
        ]);
    }
    public function actionAdd(){

//        var_dump('actionAdd',$_GET);
        $id =$_GET['id'];
        $session_id = session_id();
        $cart= new Carts(
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