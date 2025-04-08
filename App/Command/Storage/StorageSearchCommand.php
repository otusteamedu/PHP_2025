<?php

declare(strict_types=1);

namespace App\Command\Storage;

use App\Infrastructure\Storage\Storage;
use App\Model\Condition;
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
            ->setHelp('Пример, параметр selectDb опционален: ./App/bin/console storage:search --selectDb=redis --conditions="param1:paramValue1;param2:paramValue2"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arrInput = \array_filter([
            'selectDb' => $input->getOption('selectDb'),
            'conditions' => $input->getOption('conditions'),
        ], function ($item) {
            return !empty($item);
        });

        if (isset($arrInput['conditions'])) {
            $arrConditions = [];
            $conditions = \explode(';', $arrInput['conditions']);

            foreach ($conditions as $condition) {
                [$conditionName, $conditionValue] = \explode(':', $condition);
                $arrConditions[] = new Condition($conditionName, $conditionValue);
            }
        }

        try {
            if (empty($arrConditions)) {
                throw new \Exception('Нет условий для поиска');
            }

            if (isset($arrInput['selectDb'])) {
                $storage = Storage::createStorage($arrInput['selectDb']);
            } else {
                $storage = Storage::createStorage();
            }

            $event = $storage->searchEvent($arrConditions);
            $io->success('Найдено событие ' . $event->getEventInfo()->getName() . ' c приоритетом ' . $event->getPriority());
        } catch (\Exception $e) {
            $io->error('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
