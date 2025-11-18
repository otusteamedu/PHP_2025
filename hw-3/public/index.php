<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$wordCounter = new Ubunvova\WordCount\WordCounter();
$checker = new App\CheckLibrary($wordCounter);

$checker->check();
