<?php

namespace app\controllers;
use app\engine\Request;

class loginController extends Controller
{

    public function actionLogin(){

        $t = (new Request())->getParams();
//        var_dump($t);

        if($t['send']){

            $auth = new \app\engine\Authorization();
//            var_dump($auth->auth($t['login'],$t['pass']));

            $auth->login($t['login'],$t['pass'],$t['save']);
//            $auth->is_auth();
        }
    }

    public function actionLogout(){

        if (isset((new Request())->getParams()['logout'])) {

            session_destroy();
            setcookie("hash");
            $origin = $_SERVER["HTTP_REFERER"];
            header("Location: " . $origin);
        }
    }
}