<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure;

use DI\Container;
use Dinargab\Homework20\Infrastructure\Console\ConsoleController;

class App
{
    public function __construct(
        private Container $container,
    )
    {

    }

    public function run()
    {
        $controller = $this->container->get(ConsoleController::class);
        $controller->run();
    }
}