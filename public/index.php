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
use app\engine\Request;
use app\interfaces\IAuthorization;
use app\engine\Authorization;

spl_autoload_register([new Autoload(), 'loadClass']);


//var_dump('$_SERVER', $_SERVER['REQUEST_URI']);
$request = new Request();
$controllerName = $request->getControllerName()?: 'default';
$actionName = $request->getActionName() ?: 'view';

//var_dump($request->getControllerName(),$request->getActionName());

//var_dump($_POST);

$controllerClass = "app\\controllers\\" . ucfirst($controllerName) . "Controller";
//var_dump($controllerClass,$actionName);
if (class_exists($controllerClass)) {
    $controller = new $controllerClass(new Render(), new Authorization());
    $controller->runAction($actionName);
}
//Добавление нового товара
//$p=new Products("Товар7","Описание товара7", 700,"07.jpg",1,1,1);
//$p->save();

// Обновление товара
//$product2Update = Products::getOne(1);
//
//$product2Update->price = 1;
//$product2Update->description = "Новое описание товара1";
//$product2Update->name_product = "Новое название Товар1";
//$product2Update->id_product_type = 2;
//$product2Update->id_product_category = 2;
//$product2Update->id_unit = 2;
//$product2Update->save();

