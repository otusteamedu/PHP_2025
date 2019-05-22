<?php

namespace app\controllers;

use app\models\Products;

class ProductController extends Controller
{
    private $action;
    protected $layout = 'main';
    protected $useLayout = true;

    public function runAction($action = null) {
        $this->action = $action ?: 'catalog';
        $method = "action" . ucfirst($this->action);
        if (method_exists($this, $method)) {

            $this->$method();
        }
        else {
            echo "404";
        }

    }

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