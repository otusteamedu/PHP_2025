<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use User\Php2025\Validate\RequestMethod;
use User\Php2025\ValidateEmail;

$requestMethod = new RequestMethod();
$requestMethodValidate = $requestMethod->validate('POST');
if ($requestMethodValidate !== null) {
    echo $requestMethodValidate;
    exit();
}

$body = file_get_contents('php://input');
$input = json_decode($body, true);

$email = $input['email'] ?? null;
$validateEmail = new ValidateEmail();
echo $validateEmail->validate($email);
