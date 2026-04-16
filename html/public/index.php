<?php

declare(strict_types=1);

use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Kernel\Http\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env.php')) {
    require_once __DIR__ . '/../.env.php';
}

$config = require_once __DIR__ . '/../config/http.php';

$kernel = new Kernel($config);

$kernel->handle(Request::fromGlobals());
