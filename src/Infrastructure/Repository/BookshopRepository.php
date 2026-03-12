<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Enum\BookshopField;
use App\Infrastructure\Elasticsearch\Client\ElasticsearchClientProvider;
use App\Infrastructure\Elasticsearch\QueryDSL\QueryBuilder\ElasticsearchQueryBuilder;
use App\Infrastructure\Elasticsearch\QueryDSL\QueryComponent\Compound\BoolClause;
use App\Infrastructure\Elasticsearch\QueryDSL\QueryComponent\Compound\BoolQuery;
use App\Infrastructure\Elasticsearch\QueryDSL\QueryComponent\FullText\MatchQuery;
use App\Infrastructure\Elasticsearch\QueryDSL\QueryComponent\TermLevel\RangeQuery;
use App\Infrastructure\Elasticsearch\QueryDSL\QueryComponent\TermLevel\TermQuery;
use App\Model\BookshopSearchModel;
use Elastic\Elasticsearch\Client;

class BookshopRepository
{
    private readonly Client $esClient;

    public function __construct()
    {
        $this->esClient = ElasticsearchClientProvider::get();
    }

    public function createIndex(string $indexName, array $params = []): bool
    {
        return $this->esClient->indices()->create(['index' => $indexName, 'body' => $params])->asBool();
    }

    public function deleteIndex(string $indexName): bool
    {
        return $this->esClient->indices()->delete(['index' => $indexName])->asBool();
    }

    public function existsIndex(string $indexName): bool
    {
        return $this->esClient->indices()->exists(['index' => $indexName])->asBool();
    }

    public function loadBulkDocuments(array $data): bool
    {
        return $this->esClient->bulk(['body' => $data])->asBool();
    }

    public function searchDocuments(string $indexName, BookshopSearchModel $bookshopSearchModel): array
    {
        $boolQuery = new BoolQuery();

        if ($bookshopSearchModel->getCategory() !== null) {
            $boolQuery->filter(
                new TermQuery(
                    BookshopField::Category->value,
                    $bookshopSearchModel->getCategory()->value,
                )
            );
        }

        if ($bookshopSearchModel->getTitle() !== null) {
            $boolQuery->must(
                new MatchQuery(
                    BookshopField::Title->value,
                    $bookshopSearchModel->getTitle(),
                    ['fuzziness' => 'AUTO'],
                )
            );
        }

        if ($bookshopSearchModel->getMinPrice() !== null) {
            $priceConditions[RangeQuery::GTE] = $bookshopSearchModel->getMinPrice();
        }

        if ($bookshopSearchModel->getMaxPrice() !== null) {
            $priceConditions[RangeQuery::LTE] = $bookshopSearchModel->getMaxPrice();
        }

        if (!empty($priceConditions)) {
            $boolQuery->filter(new RangeQuery(BookshopField::Price->value, $priceConditions));
        }

        if ($bookshopSearchModel->getShop() !== null) {
            $boolQuery->nested(
                BoolClause::Filter,
                BookshopField::Stock->value,
                new BoolQuery()
                    ->filter(new TermQuery(BookshopField::Shop->nestedValue(), $bookshopSearchModel->getShop()->value))
                    ->filter(new RangeQuery(BookshopField::Stock->nestedValue(), [RangeQuery::GT => 0]))
            );
        }

        $query = new ElasticsearchQueryBuilder()->setQuery($boolQuery)->build();

        return $this->esClient->search(['index' => $indexName, 'body' => $query])->asArray();
    }
}
