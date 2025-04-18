<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Elastic;

use Exception;
use RuntimeException;

class ElasticIndexer
{

    private const DEFAULT_BODY = [
        'settings' => [
            'analysis' => [
                'tokenizer' => [
                    'standard' => ['type' => 'standard']
                ],
                'filter' => [
                    'russian_stop' => ['type' => 'stop', 'stopwords' => '_russian_'],
                    'russian_stemmer' => ['type' => 'stemmer', 'language' => 'russian']
                ],
                'analyzer' => [
                    'default' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['lowercase', 'russian_stop', 'russian_stemmer']
                    ]
                ]
            ]
        ],
        'mappings' => [
            'dynamic' => 'true',
            'properties' => [
                'title' => ['type' => 'text'],
                'category' => ['type' => 'text'],
                'price' => ['type' => 'float'],
                'stock' => [
                    'type' => 'nested',
                    'properties' => [
                        'shop' => ['type' => 'text'],
                        'stock' => ['type' => 'integer']
                    ]
                ]
            ]
        ]
    ];

    private ElasticClient $elastic;
    private array $paramsBody;

    public function __construct(ElasticClient $elastic)
    {
        $this->elastic = $elastic;
    }

    public function createIndex(string $index): void
    {
        $this->setBody();
        $client = $this->elastic->getClient();

        try {
            $response = $client->indices()->create([
                'index' => $index,
                'body' => $this->paramsBody
            ]);

            if ($response['acknowledged'] === true) {
                echo 'Индекс ' . $response['index'] . ' добавлен успешно!';
            } else {
                echo 'Индекс ' . $index . ' не добавлен!';
            }
        } catch (Exception $e) {
            $requestInfo = $client->transport->getLastConnection()->getLastRequestInfo();

            if (!empty($requestInfo['response']) && ($response = $requestInfo['response'])) {
                if (isset($response['error']) && ($error = $response['error']) && $error instanceof RuntimeException) {
                    echo $error->getMessage();
                }

                if (!empty($response['reason'])) {
                    echo $response['reason'];
                }
            } else {
                echo $e->getMessage();
            }
        }
    }

    public function removeIndex(string $index): void
    {

        $client = $this->elastic->getClient();

        try {
            $response = $client->indices()->delete([
                'index' => $index
            ]);

            if ($response['acknowledged'] === true) {
                echo 'Индекс ' . $index . ' удален успешно!';
            } else {
                echo 'Индекс ' . $index . ' не удален!';
            }
        } catch (Exception $e) {
            $requestInfo = $client->transport->getLastConnection()->getLastRequestInfo();

            if (!empty($requestInfo['response']) && ($response = $requestInfo['response'])) {
                if (!empty($response['error']) && ($error = $response['error']) && $error instanceof RuntimeException) {
                    echo $error->getMessage();
                }

                if (!empty($response['reason'])) {
                    echo $response['reason'];
                }
            } else {
                echo $e->getMessage();
            }
        }
    }

    private function setBody(): void
    {
        $this->paramsBody = self::DEFAULT_BODY;
    }
}
