<?php

namespace app\controllers;

use app\interfaces\IRender;
use app\models\Products;
use app\interfaces\IAuthorization;

class ProductController extends Controller
{
    public function __construct(IRender $renderer, IAuthorization $autherizator)
    {
        parent::__construct($renderer, $autherizator);
    }

    public function actionCatalog() {

        $products = Products::getAll();
        foreach ($products as &$item){

            $item['img'] = explode(',', $item['img']);
        }
//        var_dump($products);
        echo $this->render('catalog', [
            'products' => $products,
            'imgDir' => "img/gallery_img/small/"
        ]);
    }

    public function actionCard() {

        $id = $_GET['id'];
        $product = Products::getOne($id);
        $product->setImg(explode(',', $product->getImg()));
//        var_dump($product);
        echo $this->render('card', [
        'product' => $product,
        'imgDir' => "img/gallery_img/big/"
    ]);
    }
}