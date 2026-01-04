<?php

declare(strict_types=1);

use Otus\Elasticsearch\Command\IndexingCommand;
use Otus\Elasticsearch\Command\InitializeCommand;
use Otus\Elasticsearch\Command\SearchCommand;
use Otus\Elasticsearch\Kernel\Console;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Console([
    'initialize' => new InitializeCommand(),
    'indexing' => new IndexingCommand(),
    'search' => new SearchCommand(),
]);

exit($kernel->run($argv));
