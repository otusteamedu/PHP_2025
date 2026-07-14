<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Console\App;
use App\Core\Console\Factory\CommandFactoryMap;
use App\Core\Console\Metadata\CommandMetadata;
use App\Core\Container\Config\Types\Console;
use App\Core\Container\Container;
use App\Core\Container\Initialization\Initializers\ConsoleInitializer;
use App\Core\Container\Initialization\Payload\ConsolePayload;
use App\Core\Container\Initialization\Payload\PayloadInterface;
use App\Core\Utils\PathResolverInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

class ConsoleServiceProvider extends AbstractInitializableServiceProvider
{
    /**
     * @param ConsolePayload $payload
     * @note non-null guarantee is enforced by parent class
     */
    protected function doRegisterServices(Container $container, ?PayloadInterface $payload): void
    {
        // Создаем фабрики команд
        $commandFactories = $this->createCommandFactories($container, $payload->commandMetadata);

        // Оборачиваем фабрики в лоадер
        $commandLoader = new FactoryCommandLoader($commandFactories);

        // Создаем консольное приложение Symfony
        $symfonyConsoleApp = new Application();

        // Регистрируем сервисы
        $this->registerConsoleConfig($container, $payload->consoleConfig);
        $this->registerApp($container, $symfonyConsoleApp, $commandLoader);
    }

    protected function createInitializer(Container $container): ConsoleInitializer
    {
        return new ConsoleInitializer($container->get(PathResolverInterface::class));
    }

    /**
     * @param CommandMetadata[] $commandMetadata
     *
     * @return callable[]
     */
    private function createCommandFactories(Container $container, array $commandMetadata): array
    {
        $factoryMap = new CommandFactoryMap($container, $commandMetadata);

        return $factoryMap->getCommandsFactoryMap();
    }

    private function registerConsoleConfig(Container $container, Console $config): void
    {
        $container->singleton($config->getType()->value, $config);
    }

    private function registerApp(Container $container, Application $app, CommandLoaderInterface $loader): void
    {
        $container->set(App::class, static fn() => new App($app, $loader));
    }
}
