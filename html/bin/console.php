<?php

declare(strict_types=1);

use Otus\DataMapper\Command\DeleteCommand;
use Otus\DataMapper\Command\EagerCommand;
use Otus\DataMapper\Command\InsertCommand;
use Otus\DataMapper\Command\LazyCommand;
use Otus\DataMapper\Command\RunCommand;
use Otus\DataMapper\Command\UpdateCommand;
use Otus\DataMapper\Kernel\Console;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Console([
    'run' => new RunCommand(),
]);

$kernel->register('insert', new InsertCommand());
$kernel->register('lazy', new LazyCommand());
$kernel->register('eager', new EagerCommand());
$kernel->register('update', new UpdateCommand());
$kernel->register('delete', new DeleteCommand());

exit($kernel->run($argv));
