<?php

namespace Pryaniki\App;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;
class App
{
    const INDEX_NAME = 'otus-shop';
    private Client $client;

    public function __construct()
    {
        $host = getenv('ELASTICSEARCH_HOST')?:'http://elasticsearch:9200';

        $this->client = ClientBuilder::create()
            ->setHosts([$host])
            ->build();
    }

    public function createIndex(): void {
        $isExists = $this->client
            ->indices()
            ->exists(['index' => self::INDEX_NAME])
            ->asBool();
        if(!$isExists) {
            $config = [];
            $config['index'] = self::INDEX_NAME;
            $config['body'] = [
                'mappings' => $this->getMappingArray(),
                'settings' => $this->getSettingsArray()
            ];

            $this->client
                ->indices()
                ->create($config);
        }
    }
    public function resetIndex(): void
    {
        if ($this->client->indices()->exists(['index' => self::INDEX_NAME])->asBool()) {
            $this->client->indices()->delete([
                'index' => self::INDEX_NAME
            ]);
        }

        $this->createIndex();
    }
    private function getSettingsArray(): array
    {
        return [
            'analysis' => [
                'filter' => [
                    'ru_stop' => [
                        'type' => 'stop',
                        'stopwords' => '_russian_'
                    ],
                    'ru_stemmer' => [
                        'type' => 'stemmer',
                        'language' => 'russian'
                    ]
                ],
                'analyzer' => [
                    'my_russian' => [
                        'tokenizer' => 'standard',
                        'filter' => [
                            'lowercase',
                            'ru_stop',
                            'ru_stemmer'
                        ]
                    ]
                ]
            ]
        ];
    }

    private function getMappingArray(): array
    {
        return [
            'properties' => [
                'title' => [
                    'type' => 'text',
                    'analyzer' => 'my_russian'
                ],
                'sku' => [
                    'type' => 'keyword'
                ],
                'category' => [
                    'type' => 'keyword'
                ],
                'price' => [
                    'type' => 'integer'
                ],
                'stock' => [
                    'type' => 'nested',
                    'properties' => [
                        'shop' => [
                            'type' => 'keyword'
                        ],
                        'stock' => [
                            'type' => 'integer'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function search(array $args): void
    {
        $must = [];
        $filter = [];
        $should = [];


        if (!empty($args['query'])) {
            $should[] = [
                'multi_match' => [
                    'query' => $args['query'],
                    'fields' => [
                        'title'
                    ],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        $query = [
            'bool' => array_filter([
                'must' => $must,
                'filter' => $filter,
                'should' => $should,
                'minimum_should_match' => $should ? 1 : null
            ])
        ];

        if (!$should) {
            $query = ['match_all' => (object)[]];
        }

//        var_dump($query);

        $response = $this->client->search([
            'index' => self::INDEX_NAME,
            'body' => ['query' => $query]
        ]);

        $this->printTable($response['hits']['hits']);
    }

    private function limitColumnSize(string $text, int $width): string
    {
        $text = mb_strimwidth($text, 0, $width, '…');
        $padLength = $width - mb_strwidth($text);

        return $text . str_repeat(' ', max(0, $padLength));
    }

    private function printTable(array $hits): void
    {
        $wTitle = 60;
        $wCategory = 25;
        $wPrice = 8;

        echo
            $this->limitColumnSize('Название', $wTitle) . ' | ' .
            $this->limitColumnSize('Категория', $wCategory) . ' | ' .
            $this->limitColumnSize('Цена', $wPrice) . PHP_EOL;

        echo str_repeat('-', $wTitle + $wCategory + $wPrice + 6) . PHP_EOL;

        foreach ($hits as $hit) {
            $src = $hit['_source'];

            echo
                $this->limitColumnSize($src['title'], $wTitle) . ' | ' .
                $this->limitColumnSize($src['category'], $wCategory) . ' | ' .
                $this->limitColumnSize((string)$src['price'], $wPrice) . PHP_EOL;
        }
    }

    public function importFromFile(string $filePath): void
    {
        $body = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $body[] = json_decode($line, true);
        }
        $response = $this->client->bulk(['body' => $body])->asArray();

        if ($response['errors']) {
            throw new \RuntimeException('Import failed');
        }

        echo "Import completed\n";
    }

}