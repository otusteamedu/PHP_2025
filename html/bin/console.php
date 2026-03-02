<?php

declare(strict_types=1);

use Otus\Queue\Infrastructure\Kernel\Console\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../.env.php';

$config = require_once __DIR__ . '/../config/console.php';

$kernel = new Kernel($config);

$kernel->handle($argv);
