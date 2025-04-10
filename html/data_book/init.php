<?php

require __DIR__.'/../../vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()
    ->setHosts(['localhost:9200'])
    ->build();

try {
  if ($client->indices()->exists(['index' => 'otus-shop'])->asBool()) {
    echo "Index already exists. Deleting...\n";
    $client->indices()->delete(['index' => 'otus-shop']);
  }

  $params = [
      'index' => 'otus-shop',
      'body' => [
          'settings' => [
              'analysis' => [
                  'filter' => [
                      'russian_stop' => [
                          'type' => 'stop',
                          'stopwords' => '_russian_'
                      ],
                      'russian_stemmer' => [
                          'type' => 'stemmer',
                          'language' => 'russian'
                      ]
                  ],
                  'analyzer' => [
                      'russian' => [
                          'tokenizer' => 'standard',
                          'filter' => [
                              'lowercase',
                              'russian_stop',
                              'russian_stemmer'
                          ]
                      ]
                  ]
              ]
          ],
          'mappings' => [
              'properties' => [
                  'title' => [
                      'type' => 'text',
                      'analyzer' => 'russian'
                  ],
                  'sku' => [
                      'type' => 'keyword'
                  ],
                  'category' => [
                      'type' => 'text',
                      'analyzer' => 'russian',
                      'fields' => [
                          'keyword' => [
                              'type' => 'keyword'
                          ]
                      ]
                  ],
                  'price' => [
                      'type' => 'float'
                  ],
                  'stock' => [
                      'type' => 'nested',
                      'properties' => [
                          'shop' => ['type' => 'keyword'],
                          'stock' => ['type' => 'integer']
                      ]
                  ]
              ]
          ]
      ]
  ];

  $response = $client->indices()->create($params);
  echo "Index created successfully.\n";

  $client->indices()->putMapping([
      'index' => 'otus-shop',
      'body' => [
          'properties' => [
              'stock.total' => [
                  'type' => 'integer',
                  'script' => [
                      'source' => 'int total = 0; for (item in params._source.stock) { total += item.stock; } return total;'
                  ]
              ]
          ]
      ]
  ]);

  $json = file_get_contents(__DIR__.'/books.json');
  $lines = explode("\n", $json);

  $bulk = ['body' => []];
  $count = 0;

  foreach ($lines as $line) {
    if (empty(trim($line))) continue;

    $data = json_decode($line, true);
    if (json_last_error() !== JSON_ERROR_NONE) continue;

    if (isset($data['create'])) {
      $bulk['body'][] = [
          'index' => [
              '_index' => 'otus-shop',
              '_id' => $data['create']['_id']
          ]
      ];
    } else {
      $totalStock = 0;
      foreach ($data['stock'] as $stockItem) {
        $totalStock += $stockItem['stock'];
      }
      $data['stock']['total'] = $totalStock;

      $bulk['body'][] = $data;
      $count++;

      if ($count % 100 === 0) {
        $client->bulk($bulk);
        $bulk = ['body' => []];
        echo "Indexed $count documents...\n";
      }
    }
  }

  if (!empty($bulk['body'])) {
    $client->bulk($bulk);
  }

  echo "Successfully indexed $count documents.\n";

  $client->indices()->refresh(['index' => 'otus-shop']);
  echo "Index refreshed.\n";

} catch (Exception $e) {
  echo "Error: ".$e->getMessage()."\n";
  exit(1);
}