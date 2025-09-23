<?php

namespace App\Base\Routers;

use App\Base\Exceptions\Routing\InvalidRoutePath;
use App\Base\Exceptions\Routing\RouteMiddlewareExistsException;
use App\Base\Middleware\MiddlewareInterface;
use InvalidArgumentException;

final class HttpRoute
{
    /**
     * @var array<class-string<MiddlewareInterface>, true> $middlewares
     */
    private array $middlewares = [];

    private function __construct(
        private readonly HttpMethod $method,
        private readonly string $path,
        private readonly array $parameters,
        private readonly array $action
    ) {
    }

    /**
     * @throws InvalidRoutePath
     */
    public static function createAction(
        HttpMethod $method,
        string $path,
        callable|string|array $action
    ): self {
        if (!self::validateRoutePattern($path)) {
            throw new InvalidRoutePath($method, $path);
        }
        $compiledPathPattern = self::compilePattern($path);
        $parameters = self::extractParameters($path);
        return new self($method, $compiledPathPattern, $parameters, compact('action'));
    }

    private static function extractParameters(string $path): array
    {
        return preg_match_all()
    }

    private static function validateRoutePattern(
        string $path
    ): bool {
        return preg_match('#^/[a-zA-Z0-9/_-]*(\{[a-zA-Z_][a-zA-Z0-9_]*})?[a-zA-Z0-9/_-]*$#', $path) === 1;
    }

    private static function compilePattern(string $pattern): string
    {
        return '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)}#', '(?P<$1>[a-zA-Z0-9_-]+)', $pattern) . '$#';
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getAction(): callable|string|array
    {
        return $this->action['action'];
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param class-string<MiddlewareInterface> $middleware
     * @return void
     * @throws RouteMiddlewareExistsException
     */
    public function addMiddleware(string $middleware): void
    {
        if ($this->middlewares[$middleware] ?? false) {
            throw new RouteMiddlewareExistsException($this->method, $this->path, $middleware);
        } elseif (!class_exists($middleware)) {
            throw new InvalidArgumentException("Class '{$middleware}' does not exist.");
        }
        $this->middlewares[$middleware] = true;
    }
}