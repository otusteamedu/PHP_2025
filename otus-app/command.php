<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Internal\Command;

(new Command())->run($argv);
