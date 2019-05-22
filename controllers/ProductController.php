<?php

namespace app\controllers;

use app\models\Products;

class ProductController extends Controller
{
    protected $action;
    protected $layout = 'main';
    protected $useLayout = true;
    protected $defaulAction = 'catalog';

    public function actionCatalog() {
        $products = Products::getAll();
//        var_dump($products);

        echo $this->render("catalog", [
            'products' => $products
        ]);
    }

    public function actionCard() {
        $id = $_GET['id'];
        $product = Products::getOne($id);
        $product->img=explode(',', $product->img);
//        var_dump($product);die('actionCard');
        echo $this->render("card", [
            'product' => $product
        ]);
    }


}