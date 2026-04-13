<?php

declare(strict_types=1);

namespace App\Controller\Command;

use App\Domain\Service\BookshopService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:bookshop:prepare')]
class PrepareBookshopCommand extends Command
{
    private readonly BookshopService $bookshopService;

    public function __construct(?string $name = null, ?callable $code = null)
    {
        $this->bookshopService = new BookshopService();
        parent::__construct($name, $code);
    }

    public function __invoke(OutputInterface $output, #[Option] string $indexName = 'otus-shop'): int
    {
        try {
            $this->bookshopService->createIndex(
                $indexName,
                [
                    'settings' => $this->getSettings(),
                    'mappings' => $this->getMappings(),
                ],
            );
            $output->writeln(sprintf('<info>Индекс "%s" успешно создан.</info>', $indexName));

            $rawData = file($this->getDataFile());
            if ($rawData === false) {
                throw new \RuntimeException('Не удалось прочитать файл.', 400);
            }
            $preparedData = array_map(
                static fn(string $row) => json_decode($row, true, 512, JSON_THROW_ON_ERROR),
                $rawData,
            );
            $this->bookshopService->loadBulkDocuments($preparedData);
            $output->writeln(sprintf('<info>Данные из файла "%s" успешно загружены.</info>', basename($this->getDataFile())));
        } catch (\Throwable $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getDataFile(): string
    {
        return realpath(__DIR__ . '/../../var/books-39289-b51bf5.json');
    }

    private function getSettings(): array
    {
        return [
            'number_of_replicas' => 0,
            'analysis' => [
                'filter' => [
                    'ru_stop' => [
                        'type' => 'stop',
                        'stopwords' => '_russian_',
                    ],
                    'ru_stemmer' => [
                        'type' => 'stemmer',
                        'language' => 'russian',
                    ]
                ],
                'analyzer' => [
                    'ru_analyzer' => [
                        'tokenizer' => 'standard',
                        'filter' => [
                            'lowercase',
                            'ru_stop',
                            'ru_stemmer',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getMappings(): array
    {
        return [
            'properties' => [
                'title' => [
                    'type' => 'text',
                    'analyzer' => 'ru_analyzer',
                ],
                'sku' => ['type' => 'keyword'],
                'category' => ['type' => 'keyword'],
                'price' => ['type' => 'integer'],
                'stock' => [
                    'type' => 'nested',
                    'properties' => [
                        'shop' => ['type' => 'keyword'],
                        'stock' => ['type' => 'integer'],
                    ]
                ]
            ]
        ];
    }
}
