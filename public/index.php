<?php

require_once '../vendor/autoload.php';
require_once 'autoload.php';

use classes\App;

$commandsNameSpace = 'classes\\Commands\\';
$argv = $_SERVER['argv'] ?? [];

try {
    $app = new App($commandsNameSpace, $argv);
    $app->run();
}

catch (Exception $e) {
     print_r($e->getMessage());
}