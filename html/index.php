<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\OrderController;

$controller = new OrderController();
$controller->processOrder();
