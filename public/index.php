<?
include "../engine/Autoload.php";

use app\engine\{Autoload, Db};
use app\models\{Product, DigitalProduct};
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



