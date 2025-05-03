<?php

namespace App\Infrastructure\Elasticsearch;

class ElasticsearchIndexEvent extends ElasticsearchManager
{
    private const INDEX_NAME = 'otus-event';

    public function __construct()
    {
        parent::__construct(self::INDEX_NAME);
    }

    /**
     * Настройки для индекса
     */
    protected function getSettings(): array
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
                    'priority' => ['type' => 'rank_feature'],
                    'event' => [
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'text', 'analyzer' => 'my_russian'],
                        ]
                    ],
                    'conditions' => [
                        'type' => 'nested',
                        'properties' => [
                            'conditionName' => ['type' => 'keyword'],
                            'conditionValue' => ['type' => 'keyword'],
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Добавляет событие в индекс
     *
     * @param array $eventData Данные события (например, ['eventName' => '...', 'eventId' => 1])
     * @param int|null $id Идентификатор документа (если не указан, генерируется автоматически)
     *
     * @return bool
     */
    public function add(array $eventData, ?int $id = null): bool
    {
        $id = $id ?? \time();

        $document = $this->transformArrayEventDataToEsDocument($eventData);

        return $this->addDocument(
            (string)$id,
            $document
        );
    }

    private function transformArrayEventDataToEsDocument(array $data): array
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if ($key === 'conditions' && \is_array($value) && \count($value) > 0) {
                $newData[$key]['conditionName'] = \array_keys($value);
                $newData[$key]['conditionValue'] = \array_values($value);
            } else {
                $newData[$key] = $value;
            }
        }

        return $newData;
    }

    /**
     * Получает событие по его ID
     *
     * @param int $id
     * @return array
     */
    public function findById(int $id): array
    {
        return $this->getDocument((string)$id);
    }

    /**
     * Обновляет событие по его ID
     *
     * @param int $id
     * @param array $updates Данные для обновления (например, ['priority' => 200])
     * @return array
     */
    public function update(int $id, array $updates): array
    {
        return $this->updateDocument(
            (string)$id,
            $updates
        );
    }

    /**
     * Удаляет событие по его ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->deleteDocument((string)$id);
    }

    /** поиск
     * @param array $query
     * @param array $options
     * @return array
     */
    public function search(array $query, array $options = []): array
    {
        $resul = $this->searchDocument($query, $options);

        if (isset($resul['hits']['total']['value']) && $resul['hits']['total']['value'] > 0) {
            return $this->transformEsDocumentEventDataToArray($resul['hits']['hits'][0]['_source']);
        }

        return [];
    }

    private function transformEsDocumentEventDataToArray(array $data): array
    {
        $newData = [];

        foreach ($data as $key => $value) {
            if (
                $key === 'conditions' && \is_array($value) && \count($value) > 0 &&
                \is_array($value['conditionName']) && \is_array($value['conditionValue'])
            ) {
                $keys = \array_values($value['conditionName']);
                $values = \array_values($value['conditionValue']);

                $newData[$key] = \array_combine($keys, $values);
            } else {
                $newData[$key] = $value;
            }
        }

        return $newData;
    }
}
