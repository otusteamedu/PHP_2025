<?php

namespace App\Infrastructure\Elasticsearch\ElasticsearchEvent;

class ElasticsearchDeleteEvent
{
    private ElasticsearchIndexEvent $event;

    public function __construct(ElasticsearchIndexEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Удаляет событие по его ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->event->deleteDocument((string)$id);
    }
}
