<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\DI\Container;

final class App
{
    private Container $container;

    public function __construct(string $configPath = "/config/services.php")
    {
        $this->container = new Container(require dirname(__DIR__) . $configPath);
    }

    public function run(): void
    {
        $command = $this->container->get('verificationEmailCommand');
        $command->handle();
    }
}
