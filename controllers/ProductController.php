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
        echo $this->render('catalog.tmpl', [
            'products' => $products,
            'imgDir' => "img/gallery_img/small/"
        ]);
    }

    public function actionCard() {
        $id = $_GET['id'];
        $product = Products::getOne($id);
        //$product->img=explode(',', $product->img);
        echo $this->render('card.tmpl', [
        'product' => $product,
        'imgDir' => "img/gallery_img/big/"
    ]);
    }


}