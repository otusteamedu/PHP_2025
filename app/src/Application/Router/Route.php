<?php
declare(strict_types=1);

namespace App\Application\Router;

use App\Application\Http\RequestMethod;

final readonly class Route
{
    private function __construct(
        public RequestMethod $method,
        public string $path,
        public string $handler,
    ) {
    }

    public static function get(string $path, string $handler): self
    {
        return new self(RequestMethod::GET, $path, $handler);
    }

    public static function post(string $path, string $handler): self
    {
        return new self(RequestMethod::POST, $path, $handler);
    }

    public static function delete(string $path, string $handler): self
    {
        return new self(RequestMethod::DELETE, $path, $handler);
    }
}
