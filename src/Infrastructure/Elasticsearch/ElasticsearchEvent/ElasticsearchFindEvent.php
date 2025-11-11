<?php

namespace App\Infrastructure\Elasticsearch\ElasticsearchEvent;

class ElasticsearchFindEvent
{
    private ElasticsearchIndexEvent $event;

    public function __construct(ElasticsearchIndexEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Получает событие по его ID
     *
     * @param int $id
     * @return array
     */
    public function findById(int $id): array
    {
        return $this->event->getDocument((string)$id);
    }
}
