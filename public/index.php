<?php

require __DIR__ . '/../vendor/autoload.php';

header('Content-type: text/plain');

Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

$files = glob(__DIR__ . '/connection/*.php');

foreach ($files as $file) {
    require($file);
}

echo "FINISHED...\r\n";

