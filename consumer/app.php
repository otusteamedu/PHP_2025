<?php

require_once '../vendor/autoload.php';

use App\Classes\Consumer;

try {
    $app = new Consumer();
    $app->consume();
} catch (Exception $e) {
    print_r($e->getMessage());
}

