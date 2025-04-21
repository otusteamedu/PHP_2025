<?php

namespace App\Infrastructure\Console\CommandEvent;

use App\Infrastructure\Storage\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StorageAddTestDataCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('storage:addTestData')
            ->setDescription('Добавление тестовый данных в хранилище')
            ->setHelp('Пример: ./bin/console storage:addTestData');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            Storage::addTestData();
            $io->success('Тестовые данные добавлены');
        } catch (\Exception $e) {
            $io->error('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

