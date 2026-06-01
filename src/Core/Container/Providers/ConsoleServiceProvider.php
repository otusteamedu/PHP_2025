<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Console\App;
use App\Core\Console\Discovery\CommandClassFinder;
use App\Core\Console\Factory\CommandFactoryMap;
use App\Core\Console\Metadata\CommandMetadataExtractor;
use App\Core\Container\Config\Data\Console\ConsoleConfig;
use App\Core\Container\Config\Loaders\ConfigLoaderFactory;
use App\Core\Container\Config\Loaders\ConfigLoaderType;
use App\Core\Container\Container;
use App\Core\Utils\PathResolverInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function registerServices(Container $container): void
    {
        $container->set(Application::class, static fn() => new Application());

        $container->singleton(
            ConsoleConfig::class,
            static fn(Container $c) => $c
                ->get(ConfigLoaderFactory::class)
                ->create(ConfigLoaderType::CONSOLE)
                ->load(),
        );

        $container->singleton(
            CommandClassFinder::class,
            static fn(Container $c) => new CommandClassFinder(
                $c->get(ConsoleConfig::class),
                $c->get(PathResolverInterface::class),
            ),
        );

        $container->singleton(
            CommandMetadataExtractor::class,
            static fn(Container $c) => new CommandMetadataExtractor($c->get(CommandClassFinder::class)),
        );

        $container->singleton(
            CommandFactoryMap::class,
            static fn(Container $c) => new CommandFactoryMap($c),
        );

        $container->singleton(
            CommandLoaderInterface::class,
            static fn (Container $c) => new FactoryCommandLoader(
                $c->get(CommandFactoryMap::class)->getCommandsFactoryMap(),
            ),
        );

        $container->set(
            App::class,
            static fn(Container $c) => new App(
                $c->get(Application::class),
                $c->get(CommandLoaderInterface::class),
            ),
        );
    }
}
