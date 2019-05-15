<?
//use app\models\{Products, Users};


include "../engine/Autoload.php";

spl_autoload_register([new Autoload(), 'loadClass']);



$product = new Products(new Db());


var_dump($product->getOne(1));

var_dump($product instanceof Products);

