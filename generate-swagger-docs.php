<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use OpenApi\Generator;
use App\Helper;

try {
    Helper::consoleLog("Starting Swagger documentation generation...", 'INFO');
    $scanPaths = [
        __DIR__ . '/app',
        __DIR__ . '/Domain',
        __DIR__ . '/Infrastructure'
    ];

    foreach ($scanPaths as $path) {
        if (!file_exists($path)) {
            Helper::consoleLog("ERROR: Path does not exist - " . $path, 'ERROR');
            exit(1);
        }
    }

    $openapi = Generator::scan($scanPaths, [
        'exclude' => [
            __DIR__ . '/vendor',
            __DIR__ . '/tests'
        ],
        'validate' => true
    ]);

    if (!$openapi->info) {
        Helper::consoleLog("WARNING: Missing required @OA\Info() annotation", 'WARNING');
    }
    if (count($openapi->paths) === 0) {
        Helper::consoleLog("WARNING: No API paths found - check @OA\PathItem() annotations", 'WARNING');
    }

    $outputFile = __DIR__ . '/public/swagger.json';
    file_put_contents($outputFile, $openapi->toJson());

    Helper::consoleLog("Documentation saved to: " . realpath($outputFile), 'SUCCESS');
    Helper::consoleLog("Generation completed (with warnings)", 'INFO');

} catch (Exception $e) {
    Helper::consoleLog("ERROR: " . $e->getMessage(), 'ERROR');
    exit(1);
}