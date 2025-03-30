<?php

namespace App\Infrastructure\Elasticsearch;

class ShopElasticsearch
{
    private const INDEX_NAME = 'otus-shop';

    private ElasticsearchService $client;

    public function __construct(ElasticsearchService $client)
    {
        $this->client = $client;
    }

    /**
     * Создает индекс otus-shop
     *
     * @param array $settings Настройки индекса (опционально)
     */
    public function createIndex(array $settings = []): bool
    {
        return $this->client->createIndex(
            self::INDEX_NAME,
            $settings ?: $this->getDefaultSettings()
        );
    }

    /**
     * Проверяет существование индекса otus-shop
     */
    public function hasIndex(): bool
    {
        return $this->client->hasIndex(self::INDEX_NAME);
    }

    /**
     * Удаляет индекс otus-shop
     */
    public function deleteIndex(): bool
    {
        return $this->client->deleteIndex(self::INDEX_NAME);
    }

    /**
     * Настройки по умолчанию для индекса
     */
    protected function getDefaultSettings(): array
    {
        return [
            'settings' => [
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
                                'lowercase', 'ru_stop', 'ru_stemmer'
                            ]
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'my_russian'
                    ],
                    'sku' => ['type' => 'text'],
                    'category' => ['type' => 'keyword'],
                    'price' => ['type' => 'integer'],
                    'stock' => [
                        'type' => 'nested',
                        'properties' => [
                            'shop' => ['type' => 'keyword'],
                            'stock' => ['type' => 'short'],
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Добавляет товар в индекс
     *
     * @param array $productData Данные товара (например, ['title' => '...', 'price' => 19.99])
     * @param string|null $id Идентификатор документа (если не указан, генерируется автоматически)
     *
     * @return bool Ответ Elasticsearch
     */
    public function addProduct(array $productData, ?string $id = null): bool
    {
        $id = $id ?? (string) \time();

        return $this->client->addDocument(
            self::INDEX_NAME,
            $id,
            $productData
        );
    }

    /**
     * Получает товар по его ID
     */
    public function getProductById(string $id): array
    {
        return $this->client->getDocument(
            self::INDEX_NAME,
            $id
        );
    }

    /**
     * Обновляет товар по его ID
     *
     * @param array $updates Данные для обновления (например, ['price' => 200])
     */
    public function updateProduct(string $id, array $updates): array
    {
        return $this->client->updateDocument(
            self::INDEX_NAME,
            $id,
            $updates
        );
    }

    /**
     * Удаляет товар по его ID
     */
    public function deleteProduct(string $id): bool
    {
        return $this->client->deleteDocument(
            self::INDEX_NAME,
            $id
        );
    }

    public function search(array $query, array $options = []): array
    {
        return $this->client->search(
            self::INDEX_NAME,
            $query,
            $options
        );
    }


    /** Выполняет пакетные операции над товарами через ElasticsearchService
     * @param string $products
     * @return array
     */
    public function bulkProducts(string $products): array
    {
        return $this->client->bulk($products);
    }
}
