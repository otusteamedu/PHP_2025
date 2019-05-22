<?php
/**
 * Created by IntelliJ IDEA.
 * User: iluka
 * Date: 22.05.2019
 * Time: 13:31
 */

namespace app\controllers;


class CartController extends Controller
{
    private $action;
    protected $layout = 'main';
    protected $useLayout = true;
    protected $defaulAction = 'cart';

    public function actionCart() {
        $products = Products::getAll();
//        var_dump($products);

        echo $this->render("catalog", [
            'products' => $products
        ]);
    }

//    public function actionCard() {
//        $id = $_GET['id'];
//        $product = Products::getOne($id);
//        $product->img=explode(',', $product->img);
////        var_dump($product);die('actionCard');
//        echo $this->render("card", [
//            'product' => $product
//        ]);
//    }
}