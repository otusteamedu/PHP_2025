<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\UserInterface\Api\User\UserControllerFactory;

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $controller = UserControllerFactory::create();

    $id = $_GET['id'] ?? null;

    if ($id === null) {
        echo 'Введите id';
    }

    echo $controller->find($id)->email;
}
