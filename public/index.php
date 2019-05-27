<?


session_start();
include "../engine/Autoload.php";
include "../config/main.php";
require_once '../vendor/autoload.php';

use app\engine\Autoload;
use app\models\Users;
use app\utils\{Render};
use app\utils\TwigRender;
use app\models\Products;

spl_autoload_register([new Autoload(), 'loadClass']);

$controllerName = $_GET['c'] ?: 'default';
$actionName = $_GET['a'] ?: 'view';
//var_dump($_GET['a']);
//var_dump($_GET['c']);
//var_dump(Products::getOne(1));die();
$controllerClass = "app\\controllers\\" . ucfirst($controllerName) . "Controller";
//$controller = new \app\controllers\ProductController(new Render());
//$controller->runAction('catalog');

if (class_exists($controllerClass)) {
    $controller = new $controllerClass(new TwigRender());
    $controller->runAction($actionName);
}
//Добавление нового товара
//$p=new Products("Товар7","Описание товара7", 900,"07.jpg",1,1,1);
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
