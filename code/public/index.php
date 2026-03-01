<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Container\ContainerBuilder;
use App\Presentation\Controllers\Controller;

$container = ContainerBuilder::build();

$controller = $container->get(Controller::class);
$controller->run();
