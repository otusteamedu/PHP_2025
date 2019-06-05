<?php

namespace app\controllers;

use app\interfaces\IRender;
use app\models\Products;
use app\interfaces\IAuthorization;
use app\models\repositories\ProductsRepository;

class ProductController extends Controller
{
    protected $defaultAction = 'Catalog';

    /**
     * @return string
     */
    public function getDefaultAction(): string
    {
        return $this->defaultAction;
    }

    public function __construct(IRender $renderer, IAuthorization $autherizator)
    {
        parent::__construct($renderer, $autherizator);
    }

    public function actionCatalog() {

        try{

            $products = (new ProductsRepository())->getAll();
        }catch (\Exception $ex){

            echo $ex->getMessage();
        }
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
        try{

            $product = (new ProductsRepository())->getOne($id);
        }catch (\Exception $ex){

            echo "Такого товара нет";
        }
        $product->setImg(explode(',', $product->getImg()));

        echo $this->render('card', [
        'product' => $product,
        'imgDir' => "img/gallery_img/big/"
    ]);
    }
}