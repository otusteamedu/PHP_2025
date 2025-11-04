<?php

namespace Blarkinov\PhpDbCourse\Bin;

class Track
{
    public function __construct(
        private string $controller,
        private string $action,
        private string $method,
        private array $params = []
    ) {
    }

    public function __get(mixed $property)
    {
        return $this->$property;
    }
}
