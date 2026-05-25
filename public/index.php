<?php

use App\Core\Container\Builder\ContainerBuilder;
use App\Core\Http\App;

require_once __DIR__ . '/../vendor/autoload.php';

$container = ContainerBuilder::build();
$app = $container->get(App::class);

echo $app->run();
