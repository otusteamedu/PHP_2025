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
            $this->client
                ->indices()
                ->create($this->getMappingArray());
        }
    }
    private function getMappingArray(): array
    {
        return [
            'index' => self::INDEX_NAME,
            'body' => [
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text'
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
                ]
            ]
        ];
    }

    public function search(array $args): void
    {

    }

    public function importFromFile(string $filePath): void
    {
        $body = [];

        foreach (file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $body[] = json_decode($line, true);
        }

        $response = $this->client->bulk(['body' => $body]);

        if ($response->asArray()['errors']) {
            throw new \RuntimeException('Import failed');
        }

        echo "Import completed\n";
    }

}