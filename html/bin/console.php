<?php

declare(strict_types=1);

use Otus\DataMapper\Infrastructure\Kernel\Console;

require_once __DIR__ . '/../vendor/autoload.php';

$config = require_once __DIR__ . '/../config/console.php';

$kernel = new Console($config);

exit($kernel->run($argv));
