<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use DI\Container;
use Slim\App;

// Create a new DI container
$container = new Container();

// Inject dependencies
(require __DIR__ . '/dependencies.php')($container);

// Get the App instance from the container
$app = $container->get(App::class);

// Register routes
(require __DIR__ . '/routes.php')($app);

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Run the app
$app->run();
