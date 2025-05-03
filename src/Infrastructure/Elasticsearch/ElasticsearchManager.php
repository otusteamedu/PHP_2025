<?php

namespace App\Infrastructure\Elasticsearch;

use App\Infrastructure\ElasticsearchClient;
use Elastic\Elasticsearch\Client;
use Exception;

class ElasticsearchManager
{
    private Client $client;

    private string $index;

    public function __construct(string $index)
    {
        $this->index = $index;
        $this->client = ElasticsearchClient::create();
    }

    /** Проверка на существование index
     * @return bool
     */
    public function hasIndex(): bool
    {
        try {
            return $this->client->indices()->exists(['index' => $this->index])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /** Создать index. Настройки могут применяться из метода getSettings()
     * @return bool
     */
    public function createIndex(): bool
    {
        $settings = \method_exists($this, 'getSettings') ? $this->getSettings() : [];

        try {
            return $this->client->indices()->create([
                'index' => $this->index,
                'body' => $settings
            ])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /** Удалить index
     * @return bool
     */
    public function deleteIndex(): bool
    {
        try {
            return $this->client->indices()->delete(['index' => $this->index])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /** Очищает индекс от документов
     * @return bool
     */
    public function clearIndex(): bool
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'match_all' => (object)[]
                ]
            ]
        ];

        try {
            return $this->client->deleteByQuery($params)->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Выполняет пакетные операции (_bulk) из JSON-строки в формате Elasticsearch
     *
     * @param string $jsonBody JSON-строка с операциями в формате:
     *   [
     *      // добавить
     *     {"index": {"_index": "my_index", "_id": "1"}},
     *     {"title": "Статья 1", "content": "Содержимое"},
     *      // обновить
     *     {"update": {"_id": "2", "_index": "my_index"}},
     *     {"doc": {"content": "Обновленное содержимое"}},
     *      // удалить
     *     {"delete": {"_id": "3", "_index": "my_index"}}
     *   ]
     */
    public function bulk(string $jsonBody): array
    {
        try {
            return $this->client->bulk(['body' => $jsonBody])->asArray();
        } catch (Exception $e) {
            return ['error' => 'bulk - ' . $e->getMessage()];
        }
    }

    /** Добавить документ в index по id
     * @param string $id
     * @param array $document
     * @return bool
     */
    protected function addDocument(string $id, array $document): bool
    {
        try {
            return $this->client->index([
                'index' => $this->index,
                'id' => $id,
                'body' => $document
            ])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /** Получить документ из index по id
     * @param string $id
     * @return string[]
     */
    protected function getDocument(string $id): array
    {
        try {
            return $this->client->get([
                'index' => $this->index,
                'id' => $id
            ])->asArray();
        } catch (Exception $e) {
            return ['error' => 'Document not found'];
        }
    }

    /** Обновить документ в index по id
     * @param string $id
     * @param array $newData
     * @return array
     */
    protected function updateDocument(string $id, array $newData): array
    {
        try {
            return $this->client->update([
                'index' => $this->index,
                'id' => $id,
                'body' => [
                    'doc' => $newData
                ]
            ])->asArray();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /** Удалить документ из index по id
     * @param string $id
     * @return bool
     */
    protected function deleteDocument(string $id): bool
    {
        try {
            return $this->client->delete([
                'index' => $this->index,
                'id' => $id
            ])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Выполняет поиск по индексу
     *
     * @param array $query Запрос в формате Elasticsearch (например, match, bool, etc.)
     * @param array $options Дополнительные параметры (from, size, sort)
     *
     * @return array Результаты поиска
     */
    protected function searchDocument(array $query, array $options = []): array
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => $query,
            ],
        ];

        if (!empty($options)) {
            $params['body'] = \array_merge($params['body'], $options);
        }

        try {
            return $this->client->search($params)->asArray();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
