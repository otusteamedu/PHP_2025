<?php

declare(strict_types=1);

namespace App\Core\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

class App
{
    public function __construct(
        private readonly Application $symfonyConsoleApp,
        private readonly CommandLoaderInterface $commandLoader,
    ) {
    }

    public function run(): int
    {
        $this->symfonyConsoleApp->setCommandLoader($this->commandLoader);

        return $this->symfonyConsoleApp->run();
    }
}
