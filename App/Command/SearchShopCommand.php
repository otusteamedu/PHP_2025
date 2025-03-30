<?php

namespace App\Command;

use App\Infrastructure\Elasticsearch\ShopElasticsearch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchShopCommand extends Command
{
    private ShopElasticsearch $shopES;

    public function __construct(ShopElasticsearch $shopES)
    {
        parent::__construct();
        $this->shopES = $shopES;
    }

    protected function configure(): void
    {
        $this->setName('esShop:search')
            ->setDescription('Поиск данных в индексе otus-shop')
            ->addOption(
                'title',
                null,
                InputOption::VALUE_REQUIRED,
                'Поиск по названию',
                ''
            )
            ->addOption(
                'category',
                null,
                InputOption::VALUE_REQUIRED,
                'Поиск по категории',
                ''
            )
            ->addOption(
                'max-price',
                null,
                InputOption::VALUE_REQUIRED,
                'Максимальная цена книги',
                ''
            )
            ->setHelp('Пример: ./App/bin/console esShop:search --title=рыцОри --category="Исторический роман" --max-price=2000');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arrInput = \array_filter([
            'title' => $input->getOption('title'),
            'category' => $input->getOption('category'),
            'max-price' => $input->getOption('max-price'),
        ], function ($item) {
            return !empty($item);
        });

        try {
            if (!$this->shopES->hasIndex()) {
                throw new \Exception('Не создан индекс');
            }

            if (empty($arrInput)) {
                throw new \Exception('Укажите хотя бы один поисковый параметр');
            }

            $query = ['bool' => []];

            if (isset($arrInput['title'])) {
                $query['bool']['must'] = [
                    'match' => [
                        'title' => [
                            'query' => $arrInput['title'],
                            'fuzziness' => 'auto',
                        ]
                    ]
                ];
            }

            if (isset($arrInput['category'])) {
                $query['bool']['filter'][] = [
                    'term' => [
                        'category' => $arrInput['category'],
                    ]
                ];
            }

            if (isset($arrInput['max-price'])) {
                $query['bool']['filter'][] = [
                    'range' => [
                        'price' => [
                            'lt' => $arrInput['max-price'],
                        ]
                    ]
                ];
            }

            $result = $this->shopES->search($query, ['size' => 100]);

            if (isset($result['error'])) {
                throw new \Exception($result['error']);
            }

            if (isset($result['hits']['total']['value']) && $result['hits']['total']['value'] > 0) {
                $table = $io->createTable();
                $table->setHeaders(['title', 'sku', 'category', 'price', 'shop', 'stock']);

                foreach ($result['hits']['hits'] as $item) {
                    foreach ($item['_source']['stock'] as $stock) {
                        $table->addRow([
                            $item['_source']['title'],
                            $item['_source']['sku'],
                            $item['_source']['category'],
                            $item['_source']['price'],
                            $stock['shop'],
                            $stock['stock'],
                        ]);
                    }
                }

                $table->render();
            } else {
                $io->success('Нет данных или не чего не нашлось.');
            }
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
