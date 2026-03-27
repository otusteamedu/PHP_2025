<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Command;

echo (new Command())->run($argv);
