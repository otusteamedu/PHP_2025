<?php

namespace Blarkinov\PhpDbCourse\Bin;

class Route
{
    public function __construct(
        private string $path,
        private string $controller,
        private string $action,
        private string $method,
    ) {}

    public function __get($property)
    {
        return $this->$property;
    }
}
