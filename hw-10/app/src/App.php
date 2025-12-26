<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\DI\Container;
use Symfony\Component\Console\Application;

final class App
{
    private Container $container;

    public function __construct(string $configPath = "/config/services.php")
    {
        $this->container = new Container(require dirname(__DIR__) . $configPath);
    }

    public function run(): void
    {
        $searchCommand = $this->container->get('searchBooksCommand');

        $application = new Application();
        $application->addCommand($searchCommand);

        $application->run();
    }
}
