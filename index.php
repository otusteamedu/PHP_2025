<?
include "../engine/Autoload.php";

use app\engine\{Autoload, Db};
use app\models \{Product, DigitalProduct, ProductByWeight};
//use app\interfaces\{Imodel};

spl_autoload_register([new Autoload(), 'loadClass']);



$computer = new Product([
    "name" => "Компьютер",
    "price" => 1000,
    "description" => "Супер компьютер"
]);

echo $computer;
var_dump("Маржа",$computer->marja());

$computer->sell(2);

var_dump("Маржа",$computer->marja());

$computer->sell(5);

var_dump("Маржа",$computer->marja());

$digitalProduct = new DigitalProduct([
    "name" => "Цифровой товар",
    "price" => 1000,
    "description" => "Что-то цифровое"
]);
echo $digitalProduct;
var_dump("Маржа",$digitalProduct->marja());

$digitalProduct->sell(2);

var_dump("Маржа",$digitalProduct->marja());

$digitalProduct->sell(5);

var_dump("Маржа",$digitalProduct->marja());

$productByWeight = new ProductByWeight([
    "name" => "Крупа",
    "price" => 1000,
    "description" => "Весовой товар"
]);
echo $productByWeight;
var_dump("Маржа",$productByWeight->marja());

$productByWeight->sell(0.2);

var_dump("Маржа",$productByWeight->marja());

$productByWeight->sell(2.5);

var_dump("Маржа",$productByWeight->marja());



