<?php
require __DIR__ . '/vendor/autoload.php';

use Arlex2305k\Brackets\Service;

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['string'] = '()';

$request = new Service();
$request->process();
