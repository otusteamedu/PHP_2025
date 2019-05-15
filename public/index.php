<?
include "../engine/Autoload.php";
include "../interfaces/IModel.php";

use app\engine\{Autoload, Db};
use app\models\{Products};
//use app\interfaces\{Imodel};

spl_autoload_register([new Autoload(), 'loadClass']);



$product = new Products(new Db());


//var_dump($product->getOne(1));

//var_dump($product instanceof Products);

