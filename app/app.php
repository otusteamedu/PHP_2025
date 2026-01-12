<?php

use \Pryaniki\App\App;

require __DIR__ . '/vendor/autoload.php';

$app = new App();
$app->createIndex();

$command = $argv[1] ?? '';

if ($command === '') {
    echo "Usage:\n";
    echo "  php app.php import <file>\n";
    exit(1);
}

switch ($command) {
    case 'import':
        $file = $argv[2] ?? '';
        if ($file === '' || !file_exists($file)) {
            echo "File not found\n";
            exit(1);
        }
        $app->importFromFile($file);
        break;

    default:
        echo "Unknown command: $command\n";
        exit(1);
}