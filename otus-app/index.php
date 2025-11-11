<?php

use App\App;
use App\EventMapper;

require __DIR__ . './../vendor/autoload.php';

echo (new App(new EventMapper()))->run();
