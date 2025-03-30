<?php

namespace App\Command;

use App\Infrastructure\Elasticsearch\ShopElasticsearch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateIndexShopCommand extends Command
{
    private ShopElasticsearch $shopES;

    public function __construct(ShopElasticsearch $shopES)
    {
        parent::__construct();
        $this->shopES = $shopES;
    }

    protected function configure(): void
    {
        $this->setName('esShop:create-index')
            ->setDescription('Создает индекс otus-shop')
            ->setHelp('Пример: ./App/bin/console esShop:create-index');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if ($this->shopES->hasIndex()) {
                throw new \Exception('Индекс уже создан');
            }

            if (!$this->shopES->createIndex()) {
                throw new \Exception('Не удалось создать индекс');
            }

            $io->success('Индекс успешно создан');
        } catch (\Exception $e) {
            $io->error('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
