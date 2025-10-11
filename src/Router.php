<?php

namespace Blarkinov\RedisCourse;

use Exception;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): self
    {
        $this->routes[strtoupper($method)][] = [
            'regex'   => $this->patternToRegex($pattern),
            'handler' => $handler,
        ];
        return $this;
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $method = strtoupper($method);

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $uri, $matches)) {
                return call_user_func_array($route['handler'], array_slice($matches, 1));
            }
        }
        throw new Exception('route not found', 404);
    }

    private function patternToRegex(string $pattern): string
    {
        $pattern = preg_replace('/\//', '\\/', $pattern);

        return '/^' . $pattern . '$/';
    }
}
