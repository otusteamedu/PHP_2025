<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use DI\Container;
use Slim\App;
use Slim\Middleware\MethodOverrideMiddleware;

// Create a new DI container
$container = new Container();

// Inject dependencies
(require __DIR__ . '/dependencies.php')($container);

// Get the App instance from the container
$app = $container->get(App::class);

// Register routes
(require __DIR__ . '/routes.php')($app);

// Add MethodOverrideMiddleware
$methodOverrideMiddleware = new MethodOverrideMiddleware();
$app->add($methodOverrideMiddleware);

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Run the app
$app->run();
