<?php


namespace app\controllers;
use app\models\entities\Carts;
use app\interfaces\IRender;
use app\engine\Request;
use app\interfaces\IAuthorization;
use app\models\repositories\CartRepository;
use app\engine\App;

class TestController extends Controller
{
    public function __construct(IRender $renderer, IAuthorization $autherizator)
    {
        parent::__construct($renderer, $autherizator);
    }
    public function actionView() {

        $test =  App::call()->testRepository->getAll();
        
        echo $this->render("test", [
            'test' => $test
        ]);
    }
    public function actionAdd(){

//        var_dump('actionAdd',$_GET);
        $id = App::call()->request->getParams()['id'];//$_GET['id'];
        $session_id = session_id();

        $count = App::call()->cartRepository->getCount($id)[0]['count'];
        $cart=null;
        if ($count == 0){

            $cart= new Carts(
                null,
                $id,
                null,
                $session_id,
                1.
            );
//            App::call()->cartRepository->insert($cart);
        }else{

            $cart = App::call()->cartRepository->getOne($id);
            $cart->setQuantity($cart->getQuantity()+1);
//            App::call()->cartRepository->update($cart);
//            var_dump($cart);
        }
        App::call()->cartRepository->save($cart);
//        var_dump($cart);

        header("location: /product/catalog");
    }
}
