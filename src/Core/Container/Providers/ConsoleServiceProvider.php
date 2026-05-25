<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Console\App;
use App\Core\Console\CommandMapper;
use App\Core\Container\Container;
use App\Core\Utils\PathResolverInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public const string COMMAND_DIR = '/Controller/Cli/Command';
    public const string NAMESPACE_PREFIX = 'App\\Controller\\Cli\\Command\\';

    public function registerServices(Container $container): void
    {
        $container->set(Application::class, static fn() => new Application());

        $container->singleton(CommandMapper::class, static function(Container $c) {
            return new CommandMapper(
                pathResolver: $c->get(PathResolverInterface::class),
                commandDir: self::COMMAND_DIR,
                namespacePrefix: self::NAMESPACE_PREFIX,
            );
        });

        $container->set(
            CommandLoaderInterface::class,
            static function (Container $c) {
                $commandMapper = $c->get(CommandMapper::class);
                $commands = $commandMapper->getCommandsMapping();
                return new FactoryCommandLoader($commands);
            },
        );

        $container->set(
            App::class,
            static fn(Container $c) => new App(
                $c->get(Application::class),
                $c->get(CommandLoaderInterface::class)
            ),
        );
    }
}
