<?php

declare(strict_types=1);

namespace App\Core\Container\Context;

class AppContext
{
    private readonly AppLoadContext $appLoadContext;

    public function __construct()
    {
        $this->appLoadContext = $this->determineContextLoadApp();
    }

    public function getAppLoadContext(): AppLoadContext
    {
        return $this->appLoadContext;
    }

    private function determineContextLoadApp(): AppLoadContext
    {
        if (PHP_SAPI === AppLoadContext::CLI->value) {
            return AppLoadContext::CLI;
        }

        return str_starts_with($_SERVER['REQUEST_URI'], '/api/')
            ? AppLoadContext::HTTP_API
            : AppLoadContext::HTTP_WEB;
    }
}
