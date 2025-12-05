<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\EmailVerificationController;
use App\Http\Request;

$request = Request::fromGlobals();
$controller = EmailVerificationController::create();

$response = $controller->handle($request);
$response->send();
