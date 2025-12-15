<?php
require_once __DIR__ . '/../vendor/autoload.php';

use src\App;

$config = require __DIR__ . '/../config/settings.php';

$app = new App($config);

$response = $app->run();

http_response_code($response->getStatusCode());
header('Content-Type: application/json');
echo $response->getContent();
