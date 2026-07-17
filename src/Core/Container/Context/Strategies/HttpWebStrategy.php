<?php

declare(strict_types=1);

namespace App\Core\Container\Context\Strategies;

use App\Core\Container\Context\ContextType;
use App\Core\Container\Providers\HttpServiceProvider;
use App\Core\Container\Providers\UiServiceProvider;

class HttpWebStrategy implements StrategyInterface
{
    public function supports(): bool
    {
        return isset($_SERVER['REQUEST_URI']) && !str_starts_with($_SERVER['REQUEST_URI'], '/api/');
    }

    public function getContextType(): ContextType
    {
        return ContextType::HTTP_WEB;
    }

    public function getSpecificProviders(): array
    {
        return [
            UiServiceProvider::class,
            HttpServiceProvider::class,
        ];
    }
}
