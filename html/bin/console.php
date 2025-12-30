<?php

declare(strict_types=1);

use Otus\Cache\Command\RunCommand;
use Otus\Cache\Kernel\Console;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Console([
    'run' => new RunCommand(),
]);

exit($kernel->run($argv));
