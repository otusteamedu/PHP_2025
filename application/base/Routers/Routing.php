<?php

namespace App\Base\Routers;

use App\Base\BaseLogger;
use App\Base\ServiceContainer;
use InvalidArgumentException;
use ReflectionException;

final class Routing
{
    private array $routes = [];

    public function __construct()
    {
    }

    /**
     * @param array<string,callable|string> $routes
     * @return void
     */
    public function addRoutes(array $routes): void
    {
        foreach ($routes as $route => $callback) {
            $this->addRoute($route, $callback);
        }
    }

    public function addRoute(string $path, callable|string|array $callback): void
    {
        if ($this->routes[$path] ?? false) {
            throw new InvalidArgumentException("Route '{$path}' already exists.");
        }
        $this->routes[$path] = $callback;
    }

    public function dispatch(): void
    {
        $action = $this->getAction();
        if (!$action) {
            http_response_code(404);
            return;
        }

        $serviceContainer = ServiceContainer::getInstance();
        if (is_string($action)) {
            $action = $serviceContainer->make($action);
        } elseif (is_array($action)) {
            [$class, $method] = $action;
            $object = $serviceContainer->make($class);
            if (!method_exists($object, $method)) {
                throw new InvalidArgumentException("Method '{$method}' not found in class '{$class}'.");
            }
            $action = [$object, $method];
        }
        try {
            ServiceContainer::getInstance()->call($action);
        } catch (ReflectionException $e) {
            $serviceContainer->make(BaseLogger::class)->error($e);
            http_response_code(500);
        }
    }

    public function getAction(?string $path = null): callable|string|array|null
    {
        if (!$path) {
            $requestPath = $_SERVER['REQUEST_URI'] ?? '/';
            $path = parse_url($requestPath, PHP_URL_PATH);
        }
        return $this->routes[$path] ?? null;
    }
}