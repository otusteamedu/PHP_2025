<?php

declare(strict_types=1);

namespace Kamalo\EventsService;

use Kamalo\EventsService\Infrastucture\Routing\Router;

require '../vendor/autoload.php';


$router = new Router();

$router->handleRequest($_SERVER['REQUEST_URI']);