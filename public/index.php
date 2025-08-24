<?php

use App\Infrastructure\Container\ServiceContainer;
use App\Infrastructure\Http\Controller\EmailValidationController;
use App\Infrastructure\Http\Request\HttpRequest;

require __DIR__ . '/../vendor/autoload.php';

$container = new ServiceContainer();
$controller = $container->get(EmailValidationController::class);
$request = $container->get(HttpRequest::class);

echo $controller->handle($request);
