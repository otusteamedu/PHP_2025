<?php

namespace App\Command;

use App\Infrastructure\Elasticsearch\ShopElasticsearch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

class BulkShopCommand extends Command
{
    private ShopElasticsearch $shopES;

    public function __construct(ShopElasticsearch $shopES)
    {
        parent::__construct();
        $this->shopES = $shopES;
    }

    protected function configure(): void
    {
        $this->setName('esShop:load-products')
            ->setDescription('Добавляет данные в индекс otus-shop')
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_REQUIRED,
                'Путь к JSON-файлу с операциями',
                '/../data_book/books.json'
            )
            ->setHelp('Пример: ./App/bin/console esShop:load-products --file=/../data_book/books.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = __DIR__ . $input->getOption('file');

        try {
            if (!$this->isHaveFileAndReadable($filePath)) {
                throw new \Exception('Файл не найден или не доступен для чтения');
            }

            if (!$this->shopES->hasIndex()) {
                throw new \Exception('Не создан индекс');
            }

            $content = (string)\file_get_contents($filePath);
            $result = $this->shopES->bulkProducts($content);

            if (isset($result['error'])) {
                throw new \Exception($result['error']);
            }

            $io->success('Данные добавлены');
        } catch (\Exception $e) {
            $io->error('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function isHaveFileAndReadable(string $filePath): bool
    {
        return \file_exists($filePath) && \is_readable($filePath);
    }
}
