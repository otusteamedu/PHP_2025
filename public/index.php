<?php

require_once '../vendor/autoload.php';

$commandsNameSpace = 'Root\\App\\Classes\\Commands\\';
$argv = $_SERVER['argv'] ?? [];

try {
    $app = new \Root\App\Classes\App($commandsNameSpace, $argv);
    $app->run();
}

catch (Exception $e) {
     print_r($e->getMessage());
}