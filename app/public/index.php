<?php

declare(strict_types=1);


use App\App;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$config = require(dirname(__DIR__) . '/config/config.php');
$container = require(dirname(__DIR__) . '/config/container.php');

$app = new App($config, $container);

echo $app->run() . PHP_EOL;