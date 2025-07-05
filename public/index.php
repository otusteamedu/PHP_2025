<?php declare(strict_types=1);

use App\App;
use App\Core\FoodType;

require(__DIR__ . '/../vendor/autoload.php');

$app = new App();

foreach ([FoodType::BURGER, FoodType::SANDWICH, FoodType::HOT_DOG] as $type) {
    $product = $app->cook($type);
    echo "Заказ завершён, мы приготовили: " . $product->getName() . PHP_EOL . PHP_EOL;
}
