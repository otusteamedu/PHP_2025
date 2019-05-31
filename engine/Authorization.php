<?php

namespace app\engine;

use app\interfaces\IAuthorization;


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
                $params = [];
                $sql = "UPDATE `users` SET `hash` = :hash WHERE `id_user` = :id";

                $params[':hash'] = $hash;
                $params[':id'] = $id;
                Db::getInstance()->execute($sql, $params);

                setcookie("hash", $hash, time() + 3600);

            }
            $this->allow = true;
            $this->user = $this->get_user();
            header("Location: /");
        }
    }

    public function auth($login, $pass)
    {
        $sql = "SELECT * FROM users WHERE login = :login";

        $row = Db::getInstance()->queryAll($sql, [':login' => $login])[0];

        if (password_verify($pass, $row['password'])) {

            $_SESSION['login'] = $login;
            $_SESSION['id'] = $row['id_user'];

            return true;
        }
        return false;
    }

    public function is_auth()
    {
        if (isset($_COOKIE["hash"])) {
            $hash = $_COOKIE["hash"];

            $db = getDb();
            $sql = "SELECT * FROM `users` WHERE `hash`='{$hash}'";
            $result = mysqli_query($db, $sql);
            $row = mysqli_fetch_assoc($result);

            $sql = "SELECT * FROM users WHERE hash = :hash";

            $row = Db::getInstance()->queryAll($sql, [':hash' => $hash])[0];

            $user = $row['login'];
            if (!empty($user)) {
                $_SESSION['login'] = $user;
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