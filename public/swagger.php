<?php

declare(strict_types=1);

require("vendor/autoload.php");

$openapi = \OpenApi\Generator::scan([__DIR__ . '/../src']);

header('Content-Type: application/x-yaml');

echo $openapi->toYaml();