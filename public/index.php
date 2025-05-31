<?php

use App\Controller\EmailValidationController;
use App\Validator\Email\EmailValidator;

require __DIR__ . '/../vendor/autoload.php';

$controller = new EmailValidationController(new EmailValidator());
$response   = $controller->handle();

echo $response;
