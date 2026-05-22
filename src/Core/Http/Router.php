<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Controller\Http\ErrorController;
use App\Core\Utils\PathResolver;

class Router
{
    private readonly array $routes;

    public function __construct()
    {
        $this->routes = require PathResolver::build('/config/routes.php');
    }

    public function dispatch(string $requestPath, string $requestMethod): Response
    {
        $routes = array_filter($this->routes, static fn(array $params) => $params['method'] === $requestMethod);
        foreach ($routes as $params) {
            if ($params['path'] === $requestPath) {
                $controllerClassName = $params['controller'];
                $methodName = $params['action'];
                return (new $controllerClassName)->$methodName();
            }
        }

        return new ErrorController()->render404Page();
    }
}
