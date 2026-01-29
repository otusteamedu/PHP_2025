<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Console;

use Dinargab\Homework14\Application\UseCase\ImportBooksUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

#[AsCommand(name: "app:import")]
class ImportItemsCommand extends Command
{
    public function __construct(
        private ImportBooksUseCase $importBooksUseCase
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Импорт данных из файла (JSON) в ElasticSearch');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Импорт данных");

        try {
            ($this->importBooksUseCase)();
            $output->writeln("<info>Книги успешно импортированы.</info>");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>Ошибка импорта: " . $e->getMessage() . "</error>");

            return Command::FAILURE;
        }
    }
}