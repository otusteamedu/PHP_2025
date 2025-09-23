<?php
declare(strict_types=1);

namespace App\Application;

use App\Application\Http\Request;
use App\Application\Router\Router;

final readonly class Core {
    public function __construct(
        private Router $router,
    ) {
    }

    public function run(): void
    {
        $request = Request::fromGlobals();
        $routes = require 'routes.php';

        foreach ($routes as $route) {
            $this->router->add($route);
        }

        $this->router->dispatch($request)->send();
    }
}
