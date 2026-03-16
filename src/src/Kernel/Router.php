<?php

namespace Crowley\App\Kernel;

use Crowley\App\Config\Routes;

class Router
{
    private Routes $routes;
    private array $routesList;

    public function __construct(string $uri) {
        $this->routes = new Routes();
        $this->routesList = $this->routes->getRoutes();

        $this->getRout($this->routesList, $uri);
    }

    public function getRout(array $routes, string $uri): void {

        foreach ($routes as $routeUri => $route) {

            if ($routeUri === $uri) {

                $controller = $route[0];
                $method = $route[1];

                $controllerClass = "Crowley\\App\\Presenters\\Controllers\\{$controller}";

                if (!class_exists($controllerClass)) {
                    throw new \RuntimeException("Похоже вы заблудились в маршрутах {$controllerClass}");
                }

                if (!method_exists($controllerClass, $method)) {
                    throw new \RuntimeException("Данный маршрут отсутствует {$method} в {$controllerClass}");
                }

                $controller = new $controllerClass();
                $controller->$method();

                return;

            }
        }

        throw new \RuntimeException("Маршрут для URI '{$uri}' не найден");
    }

}