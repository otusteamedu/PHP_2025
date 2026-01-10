<?php

use Producer\Kernel;

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

$app = new Kernel();

$app->run();