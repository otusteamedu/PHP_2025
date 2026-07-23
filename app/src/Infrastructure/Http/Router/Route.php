<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Router;

use App\Infrastructure\Http\Handler\HandlerInterface;
use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Response\JsonResponse;

final readonly class Route
{
    public function __construct(
        private string $method,
        private string $path,
        private HandlerInterface $handler,
    ) {
    }

    public function matches(Request $request): bool
    {
        return
            $this->method === $request->getMethod()
            && $this->path === $request->getPath();
    }

    public function hasPath(string $path): bool
    {
        return $this->path === $path;
    }

    public function hasMethod(string $method): bool
    {
        return $this->method === $method;
    }

    public function execute(Request $request): JsonResponse
    {
        return $this->handler->handle($request);
    }
}