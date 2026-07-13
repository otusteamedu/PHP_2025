<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use App\Core\Utils\PathResolverInterface;
use App\Domain\BookshopSearch\BookshopService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('bookshop:index:prepare')]
class BookshopIndexPrepareCommand extends Command
{
    public function __construct(
        private readonly BookshopService $bookshopService,
        private readonly PathResolverInterface $pathResolver,
    ) {
        parent::__construct();
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option] string $indexName = 'otus-shop',
        #[Option] bool $recreateIndex = false,
    ): int {
        $this->prepareIndex($output, $indexName, $recreateIndex);

        return Command::SUCCESS;
    }

    private function prepareIndex(OutputInterface $output, string $indexName, bool $recreateIndex): void
    {
        $existsIndex = $this->existsIndex($indexName);

        if ($existsIndex && $recreateIndex === false) {
            throw new \RuntimeException(
                "Индекс '$indexName' уже существует. Для его пересоздания используйте опцию 'recreate-index'",
            );
        }

        if ($existsIndex && $recreateIndex === true) {
            $this->recreateIndex($indexName);
        }

        if (!$existsIndex) {
            $this->createIndex($indexName);
        }

        $output->writeln("<info>Индекс '$indexName' успешно создан.</info>");

        $path = $this->getBookshopDataFilePath();
        $data = $this->prepareBookshopData($path);
        $this->loadData($data);

        $output->writeln(sprintf('<info>Данные из файла "%s" успешно загружены.</info>', basename($path)));
    }

    private function existsIndex(string $indexName): bool
    {
        return $this->bookshopService->existsIndex($indexName);
    }

    private function createIndex(string $indexName): void
    {
        $options = $this->getIndexOptions();
        $this->bookshopService->createIndex($indexName, $options);
    }

    private function recreateIndex(string $indexName): void
    {
        $this->bookshopService->deleteIndex($indexName);
        $this->createIndex($indexName);
    }

    private function getIndexOptions(): array
    {
        return [
            'settings' => $this->getIndexSettings(),
            'mappings' => $this->getIndexMappings(),
        ];
    }

    private function getBookshopDataFilePath(): string
    {
        return $this->pathResolver->getVarPath() . '/books-39289-b51bf5.json';
    }

    private function prepareBookshopData(string $bookshopDataFile): array
    {
        $rawData = file($bookshopDataFile);
        if ($rawData === false) {
            throw new \RuntimeException("Не удалось прочитать файл '$bookshopDataFile'");
        }

        try {
            $preparedData = array_map(
                static fn(string $row) => json_decode(
                    json: $row,
                    associative: true,
                    flags: JSON_THROW_ON_ERROR
                ),
                $rawData,
            );
        } catch (\JsonException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return $preparedData;
    }

    private function loadData(array $preparedData): void
    {
        $this->bookshopService->loadBulkDocuments($preparedData);
    }

    private function getIndexSettings(): array
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

    private function getIndexMappings(): array
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
