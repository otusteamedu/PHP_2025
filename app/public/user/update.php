<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\UserInterface\Api\User\Request\ActualizeUserRequest;
use App\UserInterface\Api\User\UserControllerFactory;

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request = new ActualizeUserRequest(
        name: $input['name'],
        email: $input['email'],
        password: $input['password'],
        role: $input['role']
    );

    $controller = UserControllerFactory::create();
    $controller->actualize($request);
}