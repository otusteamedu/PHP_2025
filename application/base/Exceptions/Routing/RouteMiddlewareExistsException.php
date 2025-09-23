<?php

namespace App\Base\Exceptions\Routing;

use App\Base\Routers\HttpMethod;
use Exception;

class RouteMiddlewareExistsException extends Exception
{
    public function __construct(HttpMethod $httpMethod, string $path, string $middleware)
    {
        return parent::__construct(
            "Middleware '{$middleware}' already exists for '{$httpMethod->value}::{$path}'.",
            500
        );
    }
}