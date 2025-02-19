<?php

require __DIR__ . '/src/App.php';
require __DIR__ . '/src/Validator.php';
require __DIR__ . '/src/ExceptionHandler.php';

use Src\App;

header('Content-Type: application/json');

$app = new App();
echo $app->run();
