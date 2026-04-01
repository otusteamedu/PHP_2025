<?php

require __DIR__ . './../vendor/autoload.php';

use App\Internal\App;

echo (new App())->run();
