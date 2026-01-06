<?php

declare(strict_types=1);

use Otus\Cache\Command\PushCommand;
use Otus\Cache\Command\RunCommand;
use Otus\Cache\Command\SearchCommand;
use Otus\Cache\Kernel\Console;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Console([
    'run' => new RunCommand(),
]);

$kernel->register('push', new PushCommand());
$kernel->register('search', new SearchCommand());

exit($kernel->run($argv));
