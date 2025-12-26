<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application;

$response = (new Application())->run();
$response->send();
