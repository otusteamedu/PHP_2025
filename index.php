<?php

declare(strict_types=1);

use User\Php2025\Auth;
use User\Php2025\Validate;

require __DIR__ . '/vendor/autoload.php';

$auth = new Auth();
$auth->authenticate();

$body = file_get_contents('php://input');
$input = json_decode($body, true);

$validate = new Validate();
$validateString = $validate->getValidateString($input);

echo $validateString['message'];
