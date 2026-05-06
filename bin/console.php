<?php

declare(strict_types=1);

use App\Core\Console\Console;
use App\Core\Utils\PathResolver;

require_once __DIR__ . '/../vendor/autoload.php';

PathResolver::setRoot(dirname(__DIR__));

$app = new Console();
$app->run();
