<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\ProductGateway;

$pdo = require __DIR__ . '/src/bootstrap.php';

$productGateway = new ProductGateway($pdo);
$products = $productGateway->findAll();

echo "Количество продуктов: " . $products->count() . PHP_EOL;

foreach ($products as $product) {
    echo "ID: {$product['id']}, Название: {$product['name']}, Цена: {$product['price']}, Категория ID: {$product['category_id']}" . PHP_EOL;
}
