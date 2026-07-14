<?php

use App\Core\Console\App;
use App\Core\Container\Builder\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$container = ContainerBuilder::build();
$app = $container->get(App::class);

return $app->run();
