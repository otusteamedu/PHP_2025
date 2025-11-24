<?php
require __DIR__ . '/vendor/autoload.php';

use Arlex2305k\Brackets\Service;

$service = new Service();
$resultCode = $service->process();
http_response_code($resultCode);
header('');
echo $service->resultMessage . PHP_EOL;
