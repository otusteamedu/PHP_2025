<?php

namespace App\Infrastructure\Elasticsearch\ElasticsearchEvent;

class ElasticsearchUpdateEvent
{
    private ElasticsearchIndexEvent $event;

    public function __construct(ElasticsearchIndexEvent $event)
    {
        $this->event = $event;
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
        return $this->event->updateDocument(
            (string)$id,
            $updates
        );
    }
}
