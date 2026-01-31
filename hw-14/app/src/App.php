<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\DI\Container;
use App\Infrastructure\DI\ContainerBuilder;
use App\UserInterface\VerificationEmailCommand;

final class App
{
    private Container $container;

    public function __construct()
    {
        $this->container = ContainerBuilder::build();
    }

    public function run(): void
    {
        $command = $this->container->get(VerificationEmailCommand::class);
        $command->handle();
    }
}
