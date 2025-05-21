<?php

require __DIR__ . '/../vendor/autoload.php';

use App\BracketValidator;
use App\RequestHandler;

$validator = new BracketValidator();
$handler   = new RequestHandler($validator);

$handler->handle();
