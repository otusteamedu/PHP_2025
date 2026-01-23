<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Blarkinov\RedisCourse\App\App;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/events';

$_POST['priority']=56;
$_POST['conditions']=['param_3'=>10,'param_2'=>10];
$_POST['data']=['data'=>100];

(new App)->run();
