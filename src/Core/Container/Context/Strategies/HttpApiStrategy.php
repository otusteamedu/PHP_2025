<?php

declare(strict_types=1);

namespace App\Core\Container\Context\Strategies;

use App\Core\Container\Context\ContextType;
use App\Core\Container\Providers\HttpServiceProvider;

class HttpApiStrategy implements StrategyInterface
{
    public function supports(): bool
    {
        return isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/api/');
    }

    public function getContextType(): ContextType
    {
        return ContextType::HTTP_API;
    }

    public function getSpecificProviders(): array
    {
        return [
            HttpServiceProvider::class,
        ];
    }
}
