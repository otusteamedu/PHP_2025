<?php

declare(strict_types=1);

use App\Application\Console;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Console();
$app->run();
