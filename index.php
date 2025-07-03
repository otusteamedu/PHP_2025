<?php
require_once 'vendor/autoload.php';

use Elisad5791\Phpapp\IdentityMap;
use Elisad5791\Phpapp\ProductMapper;
use Elisad5791\Phpapp\Product;

$dbName = $_ENV['MYSQL_DATABASE'];
$dbUser = $_ENV['MYSQL_USER'];
$dbPassword = $_ENV['MYSQL_PASSWORD'];

$pdo = new PDO("mysql:host=db;dbname=$dbName", $dbUser, $dbPassword);
$map = new IdentityMap();
$productMapper = new ProductMapper($pdo, $map);

$product = new Product('Book', 1000);
$productMapper->save($product);

$product->setPrice(2000);
$productMapper->save($product);

$foundProduct = $productMapper->find($product->getId());
echo $foundProduct->getTitle();
$newId = $foundProduct->getId();
$productMapper->delete($product);

$product1 = new Product('Book 1', 3000);
$productMapper->save($product1);
$product2 = new Product('Book 2', 4000);
$productMapper->save($product2);
$product3 = new Product('Book 3', 5000);
$productMapper->save($product3);

$products = $productMapper->getAll();
echo '<br>-----------------------<br>';
foreach ($products as $product) {
    echo $product->getTitle();
}

$specificProducts = $productMapper->getByIds([$newId + 1, $newId + 3]);
echo '<br>-----------------------<br>';
foreach ($specificProducts as $product) {
    echo $product->getTitle();
}

$productMapper->delete($product1);
$productMapper->delete($product2);
$productMapper->delete($product3);

/*
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    price INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/