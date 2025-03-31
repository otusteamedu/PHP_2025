<?php

require_once "config.php";
require_once "src/ProductGateway.php";

$gateway = new ProductGateway($pdo);
$products = $gateway->findAll();

foreach ($products as $product) {
    echo "{$product->getId()}: {$product->getName()} - {$product->getPrice()}<br>";
}
