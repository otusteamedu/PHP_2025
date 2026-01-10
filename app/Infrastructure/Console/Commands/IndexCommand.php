<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Commands;

use App\Application\Services\IndexService;
use App\Infrastructure\Console\TableRenderer;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class IndexCommand extends Command
{
    public function __construct(
        private readonly IndexService $indexService,
        private readonly TableRenderer $tableRenderer
    ) {
        parent::__construct('index');
    }

    /**
     * Настраивает параметры команды
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Индексация книг из JSON файла')
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_REQUIRED,
                'Путь к JSON файлу с данными',
                'books.json'
            )
            ->addOption(
                'recreate',
                'r',
                InputOption::VALUE_NONE,
                'Пересоздать индекс'
            )
            ->setHelp('Примеры использования:' . PHP_EOL .
                '  php console.php index' . PHP_EOL .
                '  php console.php index --file=books.json --recreate' . PHP_EOL .
                '  php console.php index -f data.json -r'
            );
    }

    /**
     * Выполняет команду индексации
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $filePath = $input->getOption('file');
            $recreate = $input->getOption('recreate');

            if ($recreate) {
                $output->write($this->tableRenderer->renderInfo('Создание индекса...'));
                $this->indexService->createIndex();
                $output->write($this->tableRenderer->renderInfo('Индекс создан успешно.'));
            }

            $output->write($this->tableRenderer->renderInfo("Индексация файла: {$filePath}"));

            $startTime = microtime(true);
            $count = $this->indexService->indexBooksFromFile($filePath);
            $took = (microtime(true) - $startTime) * 1000;

            $output->write($this->tableRenderer->renderInfo(
                "Индексация завершена. Загружено книг: {$count} (время: {$took}мс)"
            ));

            return Command::SUCCESS;

        } catch (Exception $e) {
            $errorOutput = $this->tableRenderer->renderError($e->getMessage());
            $output->write($errorOutput);

            return Command::FAILURE;
        }
    }
}