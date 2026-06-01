<?php

declare(strict_types=1);

namespace App\Core\Container\Builder;

use App\Core\Container\Config\Loaders\ConfigLoaderFactory;
use App\Core\Container\Config\Loaders\ConfigLoaderType;
use App\Core\Container\Container;
use App\Core\Container\Context\AppContext;
use App\Core\Container\Providers\ModuleServiceProvider;
use App\Core\Container\Providers\AppLoadContextProviderFactory;
use App\Core\Container\Providers\CommonServiceProvider;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        // Регистрируем базовые сервисы из слоя Core
        new CommonServiceProvider()->registerServices($container);

        // Регистрируем сервисы специфичные для HTTP (API / Web)
        $appLoadContext = $container->get(AppContext::class)->getAppLoadContext();
        $appLoadContextProviders = new AppLoadContextProviderFactory()->createProviders($appLoadContext);
        foreach ($appLoadContextProviders as $serviceProvider) {
            $serviceProvider->registerServices($container);
        }

        // Регистрируем сервисы из слоя "Domain" и нужные им сервисы из слоя "Infrastructure" через файл конфигурации
        $configLoader = $container->get(ConfigLoaderFactory::class)->create(ConfigLoaderType::MODULES);
        new ModuleServiceProvider($configLoader)->registerServices($container);

        return $container;
    }
}
