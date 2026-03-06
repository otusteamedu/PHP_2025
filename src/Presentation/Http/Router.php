<?php
declare(strict_types=1);

namespace App\Presentation\Http;

class Router
{
    /** @var array<string, callable(Request):Response> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET ' . $path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST ' . $path] = $handler;
    }

    public function dispatch(Request $request): ?Response
    {
        $key = $request->getMethod() . ' ' . $request->getPath();
        $handler = $this->routes[$key] ?? null;

        return $handler ? $handler($request) : null;
    }
}
