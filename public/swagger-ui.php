<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;

$openapi = Generator::scan([__DIR__ . '/../app', __DIR__ . '/../domain']);

header('Content-Type: application/json');
echo $openapi->toJson();