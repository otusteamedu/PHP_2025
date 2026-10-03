<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use App\Infrastructure\RabbitMq\Topology\BankReportTopology;
use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('rabbitmq:setup-topology:bank-reports')]
class SetupBankReportTopologyCommand extends Command
{
    public function __construct(
        private readonly AmqpConnectionInterface $connection,
        private readonly BankReportTopology $topology,
    ) {
        parent::__construct();
    }

    public function __invoke(OutputInterface $output): int
    {
        try {
            $this->connection->withChannel(
                function (AMQPChannel $channel) {
                    $this->topology->declare($channel);
                },
            );

            $output->writeln('<info>Bank report topology initialized.</info>');
            return Command::SUCCESS;
        } catch (AmqpConnectionException $e) {
            $output->writeln("<error>Bank report topology initialization failed: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
