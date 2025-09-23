<?php

namespace App\Base\Exceptions\Routing;

use App\Base\Routers\HttpMethod;
use Exception;

class InvalidRoutePath extends Exception
{
    public function __construct(HttpMethod $httpMethod, string $path, ?Exception $previous = null)
    {
        parent::__construct("Route path {$httpMethod->value}::'{$path}' is not valid.", 500, $previous);
    }
}