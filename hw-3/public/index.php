<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$WordCounter = new Ubunvova\WordCount\WordCounter();
$checker = new App\CheckLibrary($WordCounter);

$checker->check();
