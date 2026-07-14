<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use App\Domain\BookshopSearch\BookshopService;
use App\Domain\BookshopSearch\Enum\BookGenre;
use App\Domain\BookshopSearch\Enum\Bookshop;
use App\Domain\BookshopSearch\Enum\BookshopField;
use App\Domain\BookshopSearch\Model\BookshopSearchModel;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('bookshop:search')]
class BookshopSearchCommand extends Command
{
    public function __construct(
        private readonly BookshopService $bookshopService,
    ) {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Option] string $indexName = 'otus-shop',
        #[Option] ?BookGenre $category = null,
        #[Option] ?string $title = null,
        #[Option] ?int $minPrice = null,
        #[Option] ?int $maxPrice = null,
        #[Option] ?Bookshop $shop = null,
    ): int {
        $model = $this->createSearchModel($category, $title, $minPrice, $maxPrice, $shop);

        try {
            $documents = $this->findDocumentsBySearchModel($model, $indexName);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $this->renderResults($output, $documents);

        return Command::SUCCESS;
    }

    private function createSearchModel(
        ?BookGenre $category,
        ?string $title,
        ?int $minPrice,
        ?int $maxPrice,
        ?Bookshop $shop,
    ): BookshopSearchModel {
        return new BookshopSearchModel($category, $title, $minPrice, $maxPrice, $shop);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    private function findDocumentsBySearchModel(BookshopSearchModel $model, string $indexName): array
    {
        return $this->bookshopService->searchDocuments($indexName, $model);
    }

    private function renderResults(OutputInterface $output, array $documents): void
    {
        $hits = $documents['hits']['hits'] ?? [];

        if (empty($hits)) {
            $output->writeln('<info>Документы не найдены. Не заданы критерии поиска?</info>');
            return;
        }

        $table = new Table($output);

        $headers = $this->getHeaders();
        $table->setHeaders($headers);

        foreach ($hits as $k => $hit) {
            $table->setRow(
                $k,
                [
                    $hit['_score'],
                    $hit['_source']['sku'],
                    $hit['_source']['title'],
                    $hit['_source']['category'],
                    $hit['_source']['price'],
                    $this->getFormatedStock($hit['_source']['stock']),
                ],
            );
        }

        $table->render();
    }

    private function getHeaders(): array
    {
        return [
            'score',
            BookshopField::Sku->value,
            BookshopField::Title->value,
            BookshopField::Category->value,
            BookshopField::Price->value,
            BookshopField::Stock->value,
        ];
    }

    private function getFormatedStock(array $stock): string
    {
        $stock = array_map(
            static function(array $nestedValue) {
                return $nestedValue[BookshopField::Shop->value]
                    . ': ' . $nestedValue[BookshopField::Stock->value];
            },
            $stock,
        );

        return implode(', ', $stock);
    }
}
