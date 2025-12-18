<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Infrastructure\RabbitMQ\RabbitMQBank;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'rabbitmq:bankConsumer')]
final class CommandBankRabbitMQConsumer extends Command
{
    private RabbitMQBank $rabbitMQBank;

    public function __construct(
        RabbitMQBank $rabbitMQBank
    )
    {
        parent::__construct();

        $this->rabbitMQBank = $rabbitMQBank;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('TEST')
            ->setHelp(
                'Пример: ./bin/console ' . $this->getName()
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->rabbitMQBank->consume($output);
        } catch (\Exception $e) {
            $io->error('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
