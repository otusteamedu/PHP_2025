<?php
declare(strict_types=1);

namespace App\Application\Router;

use App\Application\Http\Request;
use App\Application\Http\Response;
use Psr\Container\ContainerInterface;

final class Router
{
    private array $routes = [];

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function add(Route $route): void
    {
        $this->routes[$route->path][$route->method->value] = $route;
    }

    public function dispatch(Request $request): Response
    {
        $pathExists = isset($this->routes[$request->path]);
        if (!$pathExists) {
            return Response::json(['error' => 'Page Not Found'], 404);
        }

        $allowedMethods = array_keys($this->routes[$request->path] ?? []);

        if (!in_array($request->method->value, $allowedMethods, true )) {
            return Response::json([
                'error' => 'Method Not Allowed',
                'allowed' => $allowedMethods,
            ], 405, ['Allow' => implode(', ', array_keys($allowedMethods))]);
        }

        $route = $this->routes[$request->path][$request->method->value];
        assert($route instanceof Route);

        if (!$this->container->has($route->handler)) {
            return Response::json(['error' => 'Handler not found'], 500);
        }

        $handler = $this->container->get($route->handler);
        assert($handler instanceof RouteAction);

        return $handler->handle($request);
    }
}
