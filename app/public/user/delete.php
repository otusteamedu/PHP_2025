<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\UserInterface\Api\User\Request\DeleteUseRequest;
use App\UserInterface\Api\User\UserControllerFactory;

$input = json_decode(file_get_contents('php://input'), true);


if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $request = new DeleteUseRequest(
        id: $input['id']
    );

    $controller = UserControllerFactory::create();

    $controller->delete($request);
}
