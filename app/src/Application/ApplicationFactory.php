<?php
declare(strict_types=1);

namespace App\Application;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as CliApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

final class ApplicationFactory
{
    public function __invoke(ContainerInterface $c): CliApplication
    {
        $application = new CliApplication();
        $commandMap = $this->indexWithNames($this->commands());
        $commandLoader = new ContainerCommandLoader($c, $commandMap);
        $application->setCommandLoader($commandLoader);

        return $application;
    }

    /** @return Command[] */
    private function commands(): array
    {
        return require __DIR__ . '/commands.php';
    }

    /** @param Command[] $commands */
    private function indexWithNames(array $commands): array
    {
        $indexed = [];
        foreach ($commands as $command) {
            $key = $command::getDefaultName();
            $indexed[ $key ] = $command;
        }

        return $indexed;
    }
}
