<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Infrastructure\Elasticsearch\ElasticsearchEvent;
use App\Infrastructure\Elasticsearch\ElasticsearchService;
use App\Model\Condition;
use App\Model\Event;
use Exception;

class StorageEsEvent extends ElasticsearchEvent implements StorageEventInterface
{
    public function __construct(ElasticsearchService $client)
    {
        parent::__construct($client);
    }

    public function saveEvent(Event $event): void
    {
        $this->addEvent($event->toArray(), $event->getId());
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
                $conditionArr = \array_merge($conditionArr, $condition->toArray());
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

        return Event::createFromArray($this->search($query, ['size' => 1]));
    }

    public function clearEvents(): void
    {
        $this->clearIndex();
    }

    public function dataToDatabase(array $dataEvents): void
    {
        $this->deleteIndex();
        $this->createIndex();

        foreach ($dataEvents as $dataEvent) {
            if ($dataEvent instanceof Event) {
                $this->saveEvent($dataEvent);
            }
        }
    }
}
