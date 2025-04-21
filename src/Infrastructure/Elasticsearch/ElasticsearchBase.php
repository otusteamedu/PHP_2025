<?php

namespace App\Infrastructure\Elasticsearch;

use App\Infrastructure\ElasticsearchClient;
use Elastic\Elasticsearch\Client;
use Exception;

class ElasticsearchBase
{
    private Client $client;

    public function __construct()
    {
        $this->client = ElasticsearchClient::create();
    }

    /** Проверка на существование index
     * @param string $index
     * @return bool
     */
    public function hasIndex(string $index): bool
    {
        try {
            return $this->client->indices()->exists(['index' => $index])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /** Создать index
     * @param string $index
     * @param array $settings
     * @return bool
     */
    public function createIndex(string $index, array $settings = []): bool
    {
        try {
            return $this->client->indices()->create([
                'index' => $index,
                'body' => $settings
            ])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /** Удалить index
     * @param string $index
     * @return bool
     */
    public function deleteIndex(string $index): bool
    {
        try {
            return $this->client->indices()->delete(['index' => $index])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /** Очищает индекс от документов
     * @param string $index
     * @return bool
     */
    public function clearIndex(string $index): bool
    {
        $params = [
            'index' => $index,
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

    /** Добавить документ в index по id
     * @param string $index
     * @param string $id
     * @param array $document
     * @return bool
     */
    public function addDocument(string $index, string $id, array $document): bool
    {
        try {
            return $this->client->index([
                'index' => $index,
                'id' => $id,
                'body' => $document
            ])->asBool();
        } catch (Exception $e) {
            return false;
        }
    }

    /** Получить документ из index по id
     * @param string $index
     * @param string $id
     * @return string[]
     */
    public function getDocument(string $index, string $id): array
    {
        try {
            return $this->client->get([
                'index' => $index,
                'id' => $id
            ])->asArray();
        } catch (Exception $e) {
            return ['error' => 'Document not found'];
        }
    }

    /** Обновить документ в index по id
     * @param string $index
     * @param string $id
     * @param array $newData
     * @return array
     */
    public function updateDocument(string $index, string $id, array $newData): array
    {
        try {
            return $this->client->update([
                'index' => $index,
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
     * @param string $index
     * @param string $id
     * @return bool
     */
    public function deleteDocument(string $index, string $id): bool
    {
        try {
            return $this->client->delete([
                'index' => $index,
                'id' => $id
            ])->asBool();
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

    /**
     * Выполняет поиск по индексу
     *
     * @param string $index Имя индекса
     * @param array $query Запрос в формате Elasticsearch (например, match, bool, etc.)
     * @param array $options Дополнительные параметры (from, size, sort)
     *
     * @return array Результаты поиска
     */
    public function search(string $index, array $query, array $options = []): array
    {
        $params = [
            'index' => $index,
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
