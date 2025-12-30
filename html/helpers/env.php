<?php

declare(strict_types=1);

if (!function_exists('env')) {
    /**
     * @param string $env
     * @param mixed $default
     *
     * @return mixed
     */
    function env(string $env, mixed $default = null): mixed
    {
        return getenv($env) ?: $default;
    }
}
