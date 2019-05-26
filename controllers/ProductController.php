<?php

namespace app\controllers;

use app\interfaces\IRender;
use app\models\Products;

class ProductController extends Controller
{

    protected $defaulAction = 'catalog';

    public function __construct(IRender $renderer)
    {
        parent::__construct($renderer);
    }

    public function actionCatalog() {
        $products = Products::getAll();
//        var_dump($products);

        echo $this->render('catalog.tmpl', [
            products => \app\models\Products::getAll(),
            imgDir => "img/gallery_img/small/"
        ]);
    }

    public function actionCard() {
        $id = $_GET['id'];
        $product = Products::getOne($id);
        $product->img=explode(',', $product->img);
//        var_dump($product);die('actionCard');
        echo $this->render('card.tmpl', [
        product => \app\models\Products::getOne($id),
        imgDir => "img/gallery_img/big/"
    ]);
    }


}