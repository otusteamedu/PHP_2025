<?
use app\models\Products;

session_start();
include "../engine/Autoload.php";
include "../config/main.php";

use app\engine\Autoload;
use app\models\Users;

spl_autoload_register([new Autoload(), 'loadClass']);

$controllerName = $_GET['c'] ?: 'product';
$actionName = $_GET['a'];
//var_dump($_GET['a']);
//var_dump($_GET['c']);
//var_dump(Products::getOne(1));die();
$controllerClass = "app\\controllers\\" . ucfirst($controllerName) . "Controller";

if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
    $controller->runAction($actionName);
}
//Добавление нового товара
//$p=new Products("Товар5","Описание товара5", 900,"05.jpg",1,1,1);
//$p->insert();

// Обновление товара
//$product2Update = Products::getOne(1);
//
//$product2Update->price = 999;
//$product2Update->description = "Новое описание товара1";
//$product2Update->name_product = "Новое название Товар1";
//$product2Update->id_product_type = 2;
//$product2Update->id_product_category = 2;
//$product2Update->id_unit = 2;
//$product2Update->update();
