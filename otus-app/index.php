<?php

use App\App;
use App\EventMapper;

require __DIR__ . './../vendor/autoload.php';

echo (new App(new EventMapper()))->run([1]);
echo "<br>" . "<br>";
echo (new App(new EventMapper()))->run([1,2,3]);
echo "<br>" . "<br>";
echo (new App(new EventMapper()))->run([]);
