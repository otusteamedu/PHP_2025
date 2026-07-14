<?php

declare(strict_types=1);

namespace App\Core\Container\Config;

use App\Core\Container\Context\Strategies\CliStrategy;
use App\Core\Container\Context\Strategies\HttpApiStrategy;
use App\Core\Container\Context\Strategies\HttpWebStrategy;
use App\Core\Container\Context\Strategies\StrategyInterface;
use App\Core\Container\Providers\CommonServiceProvider;
use App\Core\Container\Providers\ConsoleServiceProvider;
use App\Core\Container\Providers\CoreInfrastructureServiceProvider;
use App\Core\Container\Providers\HttpServiceProvider;
use App\Core\Container\Providers\ModuleServiceProvider;
use App\Core\Container\Providers\ServiceProviderInterface;
use App\Core\Container\Providers\UiServiceProvider;

final class ContainerConfig
{
    /**
     * @return array<class-string<ServiceProviderInterface>>
     * @note Providers that are always required (regardless of application context)
     */
    public function getDefaultProviders(): array
    {
        return [
            CoreInfrastructureServiceProvider::class,
            CommonServiceProvider::class,
            ModuleServiceProvider::class,
        ];
    }

    /**
     * @return array<class-string<StrategyInterface>>
     * @note Strategies used to detect the current application context (CLI, HTTP API, HTTP Web).
     */
    public function getContextDetectionStrategies(): array
    {
        return [
            CliStrategy::class,
            HttpApiStrategy::class,
            HttpWebStrategy::class,
        ];
    }

    /**
     * @return array<class-string<ServiceProviderInterface>, int>
     * @note Priority map: higher value === earlier execution.
     */
    public function getProviderPriorities(): array
    {
        return [
            // Common infrastructure: config, paths (bootstrap deps)
            CommonServiceProvider::class => 100,
            // Core infrastructure: databases, other storages
            CoreInfrastructureServiceProvider::class => 90,
            // Optional for HTTP provider, required for Web context only
            UiServiceProvider::class => 80,
            // CLI context
            ConsoleServiceProvider::class => 70,
            // HTTP context (API or Web)
            HttpServiceProvider::class => 70,
            // Domain modules (depend on everything above)
            ModuleServiceProvider::class => 60,
        ];
    }
}
