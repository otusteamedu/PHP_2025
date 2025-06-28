<?php

declare(strict_types=1);

use User\Php2025\Auth;
use User\Php2025\Validate;

require __DIR__ . '/vendor/autoload.php';

session_start();

$auth = new Auth();
$auth->authenticate();

$body = file_get_contents('php://input');
$input = json_decode($body, true);

$validate = new Validate();
$validateString = $validate->validateString($input);

echo $validateString['message'];
