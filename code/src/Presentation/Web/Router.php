<?php

declare(strict_types=1);

namespace Queues\Presentation\Web;

use Queues\Application\Interfaces\RequestInterface;
use Queues\Application\Interfaces\ResponseInterface;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): self
    {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }

    public function post(string $path, callable $handler): self
    {
        $this->routes['POST'][$path] = $handler;
        return $this;
    }

    public function run(RequestInterface $request, ResponseInterface $response): void
    {
        $method = $request->method();
        $path = $request->path();

        if (!isset($this->routes[$method][$path])) {
            $pathExists = isset($this->routes['GET'][$path]) || isset($this->routes['POST'][$path]);
            $response->withStatus($pathExists ? 405 : 404);
            $response->json(['error' => $pathExists ? 'Method not allowed' : 'Not found']);
            return;
        }

        ($this->routes[$method][$path])($request, $response);
    }
}
