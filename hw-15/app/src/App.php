<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\DI\Container;
use App\Infrastructure\DI\ContainerBuilder;
use App\UserInterface\CreateOrderCommand;
use Symfony\Component\Console\Application;

final class App
{
    private Container $container;

    public function __construct()
    {
        $this->container = ContainerBuilder::build();
    }

    public function run(): void
    {
        $command = $this->container->get(CreateOrderCommand::class);

        $application = new Application();
        $application->addCommand($command);

        $application->run();
    }
}
