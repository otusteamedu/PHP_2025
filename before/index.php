<?php

use Core\Kernel;

require_once 'autoload.php';

session_start();

header('Content-type: application/json; charset=utf-8');

$kernel = new Kernel();
$response = $kernel->handle();
echo $response;