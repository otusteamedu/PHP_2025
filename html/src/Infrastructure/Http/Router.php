<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http;

use Otus\Queue\Infrastructure\Component\Collection;

final class Router
{
    /**
     * @var Collection
     */
    private readonly Collection $routes;

    /**
     * @var Route|null
     */
    private ?Route $fallback = null;

    public function __construct()
    {
        $this->routes = Collection::make();
    }

    /**
     * @param string $pattern
     * @param string $controller
     * @param string $action
     *
     * @return self
     */
    public function get(string $pattern, string $controller, string $action = '__invoke'): self
    {
        return $this->push(Method::GET, $pattern, $controller, $action);
    }

    /**
     * @param string $pattern
     * @param string $controller
     * @param string $action
     *
     * @return self
     */
    public function post(string $pattern, string $controller, string $action = '__invoke'): self
    {
        return $this->push(Method::POST, $pattern, $controller, $action);
    }

    /**
     * @param string $pattern
     * @param string $controller
     * @param string $action
     *
     * @return self
     */
    public function put(string $pattern, string $controller, string $action = '__invoke'): self
    {
        return $this->push(Method::PUT, $pattern, $controller, $action);
    }

    /**
     * @param string $pattern
     * @param string $controller
     * @param string $action
     *
     * @return self
     */
    public function delete(string $pattern, string $controller, string $action = '__invoke'): self
    {
        return $this->push(Method::DELETE, $pattern, $controller, $action);
    }

    /**
     * @param Method $method
     * @param string $pattern
     * @param string $controller
     * @param string $action
     *
     * @return Router
     */
    public function push(Method $method, string $pattern, string $controller, string $action = '__invoke'): self
    {
        $this
            ->routes
            ->push(
                new Route(
                    method: $method,
                    pattern: $pattern,
                    controller: $controller,
                    action: $action,
                )
            );

        return $this;
    }

    /**
     * @param string $controller
     * @param string $action
     *
     * @return self
     */
    public function fallback(string $controller, string $action = '__invoke'): self
    {
        $this->fallback = new Route(
            method: Method::GET,
            pattern: '/',
            controller: $controller,
            action: $action,
        );

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return array<Route, array>|null
     */
    public function resolve(Request $request): ?array
    {
        /** @var Route $route */
        foreach ($this->routes as $route) {
            if ($route->method !== $request->method) {
                continue;
            }

            $match = $route->match($request->uri);

            if (is_array($match)) {
                return [$route, $match];
            }
        }

        if ($this->fallback instanceof Route) {
            return [$this->fallback, []];
        }

        return null;
    }
}
