<?
use app\models\Products;


include "../engine/Autoload.php";
include "../config/main.php";

use app\engine\Autoload;
use app\models\Product;


spl_autoload_register([new Autoload(), 'loadClass']);


// Получение всех товаров
$prods=new Products();
// Создание нового товара
$newprod = new Product([

    name_product        => 'Товар3',
    description         => 'Оприсание товара3',
    price               => 555,
    name_unit           => 'шт.',
    img                 => 'kdfls',
    type                => 'Физический',
    category            => 'Одежда'
]);
// Добавление нового товара в БД
$prods->insert($newprod);
// Обновление созданного товара
$update = $prods->getOne($prods->products[count($prods->products)-1]);
$update->name_product = "Updated";
$update->price = "666";
$update->description = "Новое описание";
//$update->name_unit = "кг"; // по этому полю обновление не работает ((
$update->img = "новый";
$update->type = "Весовой";
$update->category = "Техника";
$prods->update($update);
var_dump('Все продукты',$prods);

//Удаление товара из БД
//$prods->delete($prods->products[count($prods->products)-1]);
var_dump('Все продукты',$prods);

