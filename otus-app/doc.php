<?php

require __DIR__ . './../vendor/autoload.php';

use OpenApi\Generator;

$openapi = (new Generator())->generate([__DIR__ . '/src/Controller']);

header('Content-Type: application/x-yaml');

echo $openapi->toYaml();
