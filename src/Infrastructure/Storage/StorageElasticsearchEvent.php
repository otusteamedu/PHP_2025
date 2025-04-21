<?php

namespace App\Infrastructure\Storage;

use App\Domain\Entity\Condition;
use App\Domain\Entity\Event;
use App\Infrastructure\Elasticsearch\ElasticsearchIndexEvent;
use App\Infrastructure\Mapper\ConditionMapper;
use App\Infrastructure\Mapper\EventMapper;
use Exception;

class StorageElasticsearchEvent implements StorageEventDBInterface
{
    private ElasticsearchIndexEvent $client;
    public function __construct()
    {
        $this->client = new ElasticsearchIndexEvent();
    }

    public function saveEvent(Event $event): void
    {
        $this->client->addEvent(EventMapper::toArray($event), $event->getId());
    }

    /**
     * @param Condition[] $searchConditions
     * @return Event
     * @throws Exception
     */
    public function searchEvent(array $searchConditions): Event
    {
        $conditionArr = [];

        foreach ($searchConditions as $condition) {
            if ($condition instanceof Condition) {
                $conditionArr = \array_merge($conditionArr, ConditionMapper::toArray($condition));
            }
        }

        if (empty($condition)) {
            if (empty($arrInput)) {
                throw new Exception('Укажите хотя бы один поисковый параметр');
            }
        }

        $query = [];

        if (!empty($conditionArr)) {
            $query['bool']['must']['nested']['path'] = 'conditions';

            foreach ($conditionArr as $conditionKey => $conditionValue) {
                $query['bool']['must']['nested']['query']['bool']['must'][] = ['match' => ['conditions.conditionName' => $conditionKey]];
                $query['bool']['must']['nested']['query']['bool']['must'][] = ['match' => ['conditions.conditionValue' => $conditionValue]];
            }

            $query['bool']['should']['rank_feature'] = [
                'field' => 'priority',
                'boost' => 10,
            ];
        }

        return EventMapper::createFromArray($this->client->search($query, ['size' => 1]));
    }

    public function clearEvents(): void
    {
        $this->client->clearIndex();
    }

    /** Для пересоздания индекса и добавления данных
     * @param Event[] $dataEvents
     * @return void
     */
    public function dataToDatabase(array $dataEvents): void
    {
        $this->client->deleteIndex();
        $this->client->createIndex();

        foreach ($dataEvents as $dataEvent) {
            if ($dataEvent instanceof Event) {
                $this->saveEvent($dataEvent);
            }
        }
    }
}
