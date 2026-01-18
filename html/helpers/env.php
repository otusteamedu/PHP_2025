<?php

declare(strict_types=1);

use Otus\DataMapper\Config\ConfigInterface;
use Otus\DataMapper\Di\UnresolveParameterException;

if (!function_exists('env')) {
    /**
     * @param string $env
     * @param mixed|null $default
     *
     * @return mixed
     *
     * @throws ReflectionException
     * @throws UnresolveParameterException
     */
    function env(string $env, mixed $default = null): mixed
    {
        return resolve(ConfigInterface::class)->get($env, $default);
    }
}
