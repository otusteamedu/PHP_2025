<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\DI\Container;
use App\Infrastructure\DI\ContainerBuilder;
use App\UserInterface\GetBankStatement;
use App\UserInterface\StartBankStatementConsumer;
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
        $argv = $_SERVER['argv'] ?? [];
        $mode = $argv[1] ?? 'console';

        match ($mode) {
            'get-bank-statement' => $this->getBankStatement(),
            'start-bank-statement-consumer' => $this->startBankStatementConsumer(),
        };
    }

    private function getBankStatement(): void
    {
        $command = $this->container->get(GetBankStatement::class);

        $application = new Application();
        $application->addCommand($command);

        $application->run();
    }

    private function startBankStatementConsumer(): void
    {
        $producer = $this->container->get(StartBankStatementConsumer::class);

        $producer->execute();
    }
}
