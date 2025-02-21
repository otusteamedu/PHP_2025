<?php

use App\App;

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

$app = new App();

$app->run();