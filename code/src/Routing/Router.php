<?php

declare(strict_types=1);

namespace Ak\Hw\Routing;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            
            $controllerClass = $handler[0];
            $methodName = $handler[1];

            // Класс контроллера уже должен быть загружен (например, в index.php)
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller class not found: {$controllerClass}", 500);
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $methodName)) {
                throw new \Exception("Method {$methodName} not found in controller {$controllerClass}", 500);
            }

            // Вызываем метод контроллера
            $controller->$methodName();
        } else {
            throw new \Exception('Route not found', 404);
        }
    }
}