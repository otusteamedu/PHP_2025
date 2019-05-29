<?php

namespace app\controllers;

use app\interfaces\IRender;
use app\models\Products;

class ProductController extends Controller
{
    public function __construct(IRender $renderer)
    {
        parent::__construct($renderer);
    }

    public function actionCatalog() {

        $products = Products::getAll();
        foreach ($products as &$item){

            $item['img'] = explode(',', $item['img']);
        }
        echo $this->render('catalog', [
            'products' => $products,
            'imgDir' => "img/gallery_img/small/"
        ]);
    }

    public function actionCard() {
        $id = $_GET['id'];
        $product = Products::getOne($id);
        $product->img=explode(',', $product->img);
//        var_dump($product);
        echo $this->render('card', [
        'product' => $product,
        'imgDir' => "img/gallery_img/big/"
    ]);
    }


}