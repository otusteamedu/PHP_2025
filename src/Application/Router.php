<?php

declare(strict_types=1);

namespace App\Application;

use App\Controller\ErrorController;

class Router
{
    private readonly array $routes;

    public function __construct()
    {
        $this->routes = require __DIR__ . '/../../config/routes.php';
    }

    public function dispatch(string $requestPath, string $requestMethod): Response
    {
        $routes = array_filter($this->routes, static fn(array $params) => $params[0] === $requestMethod);
        foreach ($routes as $params) {
            if ($params[1] === $requestPath) {
                $controllerClassName = "\\$params[2]";
                $methodName = $params[3];
                return (new $controllerClassName)->$methodName();
            }
        }

        return new ErrorController()->render404Page();
    }
}
