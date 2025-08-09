<?php

require_once __DIR__ . '/src/App.php';
require_once __DIR__ . '/src/Auth.php';
require_once __DIR__ . '/src/Validator.php';
require_once __DIR__ . '/src/Response.php';
require_once __DIR__ . '/src/Exceptions/EmptyStringException.php';
require_once __DIR__ . '/src/Exceptions/InvalidCharacterException.php';

$app = new App();
echo $app->run();