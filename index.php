<?php

require_once "config.php";
require_once "src/ProductGateway.php";

$gateway = new ProductGateway($pdo);

foreach ($gateway->findAll(1000) as $product) {
    echo "{$product->getId()}: {$product->getName()} - {$product->getPrice()}<br>";
}
