<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Infrastructure\Redis\RedisService;
use App\Model\Condition;
use App\Model\Event;
use Exception;

class StorageRedisEvent extends RedisService implements StorageEventInterface
{
    public function saveEvent(Event $event): void
    {
        $eventArr = $event->toArray();
        $eventJson = \json_encode($eventArr, JSON_UNESCAPED_UNICODE);
        $eventKey = 'event:' . $event->getId();

        $this->set($eventKey, $eventJson);

        foreach ($event->getConditionsToRedisData() as $conditionKey) {
            $this->zAdd($conditionKey, $event->getPriority(), $eventKey);
        }
    }

    public function searchEvent(array $searchConditions): Event
    {
        $conditionSearch = [];

        foreach ($searchConditions as $searchCondition) {
            if ($searchCondition instanceof Condition) {
                $conditionSearch[] = $searchCondition->getKeyForRedis();
            }
        }

        $nameTempDst = 'eventSearched';
        $this->zInterStore($nameTempDst, $conditionSearch, null, 'max');
        $resultRange = $this->zRange($nameTempDst, -1, 1, true);
        $this->del($nameTempDst);

        if ($resultRange === false) {
            throw new Exception('Нет совпадений по условию(ям)');
        }

        $eventKey = \array_key_first($resultRange);

        if ($eventKey === null) {
            return Event::createFromArray([]);
        }

        $eventData = $this->get($eventKey);

        if ($eventData === '(nil)') {
            return Event::createFromArray([]);
        }

        $eventData = \json_decode($eventData, true);

        return Event::createFromArray($eventData);
    }

    public function clearEvents(): void
    {
        $this->flushDB();
    }

    public function dataToDatabase(array $dataEvents): void
    {
        $this->clearEvents();

        foreach ($dataEvents as $dataEvent) {
            if ($dataEvent instanceof Event) {
                $this->saveEvent($dataEvent);
            }
        }
    }
}
