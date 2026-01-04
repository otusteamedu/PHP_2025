<?php

declare(strict_types=1);

use Otus\Elasticsearch\Command\ReindexCommand;
use Otus\Elasticsearch\Kernel\Console;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Console([
    'reindex' => new ReindexCommand(),
]);

exit($kernel->run($argv));
