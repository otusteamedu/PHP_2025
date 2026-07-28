<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Router;

use App\Infrastructure\Http\Handler\HandlerInterface;
use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Response\JsonResponse;

final class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    public function get(string $path, HandlerInterface $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, HandlerInterface $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, HandlerInterface $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, HandlerInterface $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(
        string $method,
        string $path,
        HandlerInterface $handler,
    ): void
    {
        $this->routes[] = new Route(
            method: $method,
            path: $path,
            handler: $handler,
        );
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                $route
                    ->execute($request)
                    ->send();

                return;
            }
        }

        foreach ($this->routes as $route) {
            if ($route->hasPath($request->getPath())) {
                (new JsonResponse([
                    'message' => 'Method not allowed',
                ], 405))->send();

                return;
            }
        }

        (new JsonResponse([
            'message' => 'Route not found',
        ], 404))->send();
    }
}
