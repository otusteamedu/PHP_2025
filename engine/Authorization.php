<?php

namespace app\engine;

use app\interfaces\IAuthorization;
use app\models\entities\Users;


class Authorization implements IAuthorization
{
//session_start();
//define("SALT", "tfg45gyj4ggdgsag4534g4hergsdf34tgeg");
 public $allow = false;
 private $user = '';
//
//if (isset($_GET['logout'])) {
//    session_destroy();
//    setcookie("hash");
//    header("Location: /");
//}

//if (is_auth()) {
//    $allow = true;
//    $user = get_user();
//}


//if (isset($_GET['send'])) {
//    $login = $_GET['login'];
//    $pass = $_GET['pass'];
    public function __construct()
    {

    }

    public function logIn($login, $pass, $save){

        if (!$this->auth($login, $pass)) {
            Die('Не верный логин пароль');
        } else {
            if (isset($save)) {

                $hash = uniqid(rand(), true);
                $id = $_SESSION['id'];
                $current_user = App::call()->userRepository->getOne($id);
//                var_dump($current_user);die();
                $current_user->setHash($hash);
                App::call()->userRepository->update($current_user);

                setcookie("hash", $hash, time() + 3600,'/');

            }
            $this->allow = true;
            $this->user = $this->get_user();
            $origin = $_SERVER["HTTP_REFERER"];
            header("Location: " . $origin);
        }
    }

    public function auth($login, $pass)
    {
        $user = App::call()->userRepository->getObject($login);
//var_dump($user);die();
        if (password_verify($pass, $user->password)) {

            $_SESSION['login'] = $login;
            $_SESSION['id'] = $user->id_user;

            return true;
        }
        return false;
    }

    public function is_auth()
    {
        if (isset($_COOKIE["hash"])) {
            $hash = $_COOKIE["hash"];
            $user = App::call()->userRepository->getUserByHash($hash);
//            var_dump('AUTHERIZATION - is_auth',$user);die();
            $login = $user->login;
            if (!empty($login)) {
                $_SESSION['login'] = $login;
            }
        }
        return isset($_SESSION['login']) ? true : false;
    }

    public function get_user()
    {
        return $this->is_auth() ? $_SESSION['login'] : false;
    }
    public function __toString()
    {
        return "Authorization";
    }
//if (!$allow) echo "
//<form>
//	<input type='text' name='login' placeholder='Логин'>
//	<input type='password' name='pass' placeholder='Пароль'>
//	Save? <input type='checkbox' name='save'>
//	<input type='submit' name='send'>
//</form>
//";
//else echo "Добро пожаловать, {$user} <a href='?logout'>выход</a>";



}