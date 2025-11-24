<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\DI\Container;
use App\UserInterface\HttpResponse;

final class App
{
    private Container $container;

    public function __construct(string $configPath = "/config/services.php")
    {
        $this->container = new Container(require dirname(__DIR__) . $configPath);
    }

    public function run(): HttpResponse
    {
        $command = $this->container->get('httpController');

        return $command->handle();
    }
}
