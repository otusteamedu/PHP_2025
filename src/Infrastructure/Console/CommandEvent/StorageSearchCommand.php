<?php

namespace App\Infrastructure\Console\CommandEvent;

use App\Application\UseCase\SearchEvent;
use App\Infrastructure\Mapper\ConditionMapper;
use App\Infrastructure\Storage\StorageEvent;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StorageSearchCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('storage:search')
            ->setDescription('Поиск данных в хранилище')
            ->addOption(
                'selectDb',
                null,
                InputOption::VALUE_OPTIONAL,
                'Выбор хранилища данных',
                ''
            )
            ->addOption(
                'conditions',
                null,
                InputOption::VALUE_REQUIRED,
                'Поиск по входящим условиям',
                ''
            )
            ->setHelp('Пример, параметр selectDb опционален: ./bin/console storage:search --selectDb=redis --conditions="param1:paramValue1;param2:paramValue2"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $arrConditions = ConditionMapper::createFromString($input->getOption('conditions'));
            $storage = StorageEvent::createStorage($input->getOption('selectDb'));
            $event = SearchEvent::execute($storage, $arrConditions);

            $io->success('Найдено событие ' . $event->getEventInfo()->getName() . ' c приоритетом ' . $event->getPriority()->getValue());
        } catch (Exception $e) {
            $io->error('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
