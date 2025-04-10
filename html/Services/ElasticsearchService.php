<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
  private $client;

  public function __construct()
  {
    $this->client = ClientBuilder::create()
        ->setHosts(['localhost:9200'])
        ->build();
  }

  public function searchBooks($query, $category, $maxPrice, $inStock)
  {
    $searchParams = [
        'index' => 'otus-shop',
        'body' => [
            'query' => [
                'bool' => [
                    'must' => [],
                    'filter' => []
                ]
            ]
        ]
    ];

    if (!empty($query)) {
      $searchParams['body']['query']['bool']['must'][] = [
          'multi_match' => [
              'query' => $query,
              'fields' => ['title', 'category'],
              'fuzziness' => 'AUTO'
          ]
      ];
    }

    if (!empty($category)) {
      $searchParams['body']['query']['bool']['filter'][] = [
          'term' => ['category.keyword' => $category]
      ];
    }

    if (!empty($maxPrice)) {
      $searchParams['body']['query']['bool']['filter'][] = [
          'range' => ['price' => ['lte' => (float)$maxPrice]]
      ];
    }

    if ($inStock) {
      $searchParams['body']['query']['bool']['filter'][] = [
          'range' => ['stock.total' => ['gt' => 0]]
      ];
    }

    try {
      return $this->client->search($searchParams)['hits']['hits'] ?? [];
    } catch (Exception $e) {
      throw new \RuntimeException('Search error: ' . $e->getMessage());
    }
  }
}