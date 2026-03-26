#!/usr/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;

$srcPath = __DIR__ . '/../src';
$docsPath = __DIR__ . '/../public/docs';

$openapi = Generator::scan([$srcPath]);

$yamlContent = $openapi->toYaml();
file_put_contents($docsPath . '/openapi.yaml', $yamlContent);

$jsonContent = $openapi->toJson();
file_put_contents($docsPath . '/openapi.json', $jsonContent);

echo "Документация сгенерирована:\n";
echo "  - {$docsPath}/openapi.yaml\n";
echo "  - {$docsPath}/openapi.json\n";
