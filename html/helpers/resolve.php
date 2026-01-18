<?php

declare(strict_types=1);

use Otus\DataMapper\Di\Container;
use Otus\DataMapper\Di\UnresolveParameterException;

if (!function_exists('resolve')) {
    /**
     * @param string $class
     *
     * @return object
     *
     * @throws ReflectionException
     * @throws UnresolveParameterException
     */
    function resolve(string $class): object
    {
        return Container::getInstance()->get($class);
    }
}
