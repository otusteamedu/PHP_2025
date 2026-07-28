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
        if ($this->method !== $request->getMethod()) {
            return false;
        }

        $routeParts = explode('/', trim($this->path, '/'));
        $requestParts = explode('/', trim($request->getPath(), '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        foreach ($routeParts as $index => $routePart) {

            $requestPart = $requestParts[$index];

            if (
                str_starts_with($routePart, '{')
                && str_ends_with($routePart, '}')
            ) {
                $parameter = trim($routePart, '{}');

                $request->setAttribute(
                    $parameter,
                    $requestPart,
                );

                continue;
            }

            if ($routePart !== $requestPart) {
                return false;
            }
        }

        return true;
    }

    public function hasPath(string $path): bool
    {
        $routeParts = explode('/', trim($this->path, '/'));
        $requestParts = explode('/', trim($path, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        foreach ($routeParts as $index => $routePart) {

            if (
                str_starts_with($routePart, '{')
                && str_ends_with($routePart, '}')
            ) {
                continue;
            }

            if ($routePart !== $requestParts[$index]) {
                return false;
            }
        }

        return true;
    }

    public function execute(Request $request): JsonResponse
    {
        return $this->handler->handle($request);
    }
}
