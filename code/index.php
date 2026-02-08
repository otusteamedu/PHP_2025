<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Ak\Hw\Container;
use Ak\Hw\Presentation\Controller\IndexController;

// DI Container
$container = new Container();

// Create and run the controller
$controller = new IndexController($container);
$controller->handleRequest();
