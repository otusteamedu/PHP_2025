<?php

declare(strict_types=1);

use Otus\DataMapper\Kernel\Console;
use Otus\DataMapper\Sql\Command\DeleteCommand;
use Otus\DataMapper\Sql\Command\EagerCommand;
use Otus\DataMapper\Sql\Command\InsertCommand;
use Otus\DataMapper\Sql\Command\LazyCommand;
use Otus\DataMapper\Sql\Command\RunCommand;
use Otus\DataMapper\Sql\Command\SqlCommand;
use Otus\DataMapper\Sql\Command\UpdateCommand;

require_once __DIR__ . '/../vendor/autoload.php';

$kernel = new Console([
    'run' => new RunCommand(),
]);

$kernel->register('sql', new SqlCommand());
$kernel->register('insert', new InsertCommand());
$kernel->register('lazy', new LazyCommand());
$kernel->register('eager', new EagerCommand());
$kernel->register('update', new UpdateCommand());
$kernel->register('delete', new DeleteCommand());

exit($kernel->run($argv));
