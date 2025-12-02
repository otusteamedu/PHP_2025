<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Dinargab\Homework20\Infrastructure\App;
require __DIR__ . '/vendor/autoload.php';
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/definitions.php');
$container = $containerBuilder->build();

(new App($container))->run();