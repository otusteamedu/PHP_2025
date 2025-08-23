<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch;

use Exception;

final class IndexManager
{
    public function __construct(
        private ElasticsearchClient $client
    ) {
    }

    /**
     * Создает индекс с настройками
     */
    public function createIndex(): void
    {
        $params = [
            'index' => $this->client->getIndexName(),
            'body' => $this->client->getIndexSettings()
        ];

        $this->client->getClient()->indices()->create($params);
    }

    /**
     * Проверяет существование индекса
     */
    public function indexExists(): bool
    {
        try {
            $params = [
                'index' => $this->client->getIndexName()
            ];

            return $this->client->getClient()->indices()->exists($params)->asBool();
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Удаляет индекс
     */
    public function deleteIndex(): void
    {
        if ($this->indexExists()) {
            $params = [
                'index' => $this->client->getIndexName()
            ];

            $this->client->getClient()->indices()->delete($params);
        }
    }

    /**
     * Обновляет индекс для применения изменений
     */
    public function refreshIndex(): void
    {
        $params = [
            'index' => $this->client->getIndexName()
        ];

        $this->client->getClient()->indices()->refresh($params);
    }
}