<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Command;

(new Command())->run($argv);
