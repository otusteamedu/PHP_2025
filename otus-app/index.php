<?php

require __DIR__ . './../vendor/autoload.php';

use App\App;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo 'Invalid request method';

    return;
}

$app = new App();
$app->handleRequest();
