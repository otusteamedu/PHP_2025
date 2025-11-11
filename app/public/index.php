<?php

declare(strict_types=1);

use App\Application;

$root = __DIR__;

require($root . '/../vendor/autoload.php');
$config = require($root . '/../config/config.php');

(new Application($config))->run();
