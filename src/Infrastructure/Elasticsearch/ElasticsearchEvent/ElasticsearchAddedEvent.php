<?php

namespace App\Infrastructure\Elasticsearch\ElasticsearchEvent;

class ElasticsearchAddedEvent
{
    private ElasticsearchIndexEvent $event;

    public function __construct(ElasticsearchIndexEvent $event)
    {
        $this->event = $event;
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

        return $this->event->addDocument(
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
}
