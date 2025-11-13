<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Internal\Command;

echo (new Command())->run($argv);
