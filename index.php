<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Infrastructure\Controllers\RequestController;

$controller = new RequestController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $controller->showNote();
} else {
    $controller->showForm();
}