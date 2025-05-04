<?php

namespace App\Infrastructure\Storage;

use App\Domain\Entity\Condition;
use App\Domain\Entity\Event;
use App\Infrastructure\Mapper\ConditionMapper;
use App\Infrastructure\Mapper\EventMapper;
use App\Infrastructure\RedisClient;
use Exception;
use Redis;

class StorageRedisEvent implements StorageEventDBInterface
{
    private Redis $client;

    public function __construct()
    {
        $this->client = RedisClient::create();
    }

    public function saveEvent(Event $event): void
    {
        $eventArr = EventMapper::toArray($event);
        $eventJson = \json_encode($eventArr, JSON_UNESCAPED_UNICODE);
        $eventKey = 'event:' . $event->getId();

        $this->client->set($eventKey, $eventJson);

        $conditionsRedis = $event->getConditions();

        foreach (ConditionMapper::getConditionsToRedisData($conditionsRedis) as $conditionKey) {
            $this->client->zAdd($conditionKey, $event->getPriority()->getValue(), $eventKey);
        }
    }

    /**
     * @throws Exception
     */
    public function searchEvent(array $searchConditions): Event
    {
        $conditionSearch = [];

        foreach ($searchConditions as $searchCondition) {
            if ($searchCondition instanceof Condition) {
                $conditionSearch[] = ConditionMapper::getKeyForRedis($searchCondition);
            }
        }

        $nameTempDst = 'eventSearched';
        $this->client->zInterStore($nameTempDst, $conditionSearch, null, 'max');
        $resultRange = $this->client->zRange($nameTempDst, -1, 1, true);
        $this->client->del($nameTempDst);

        if ($resultRange === false) {
            throw new Exception('Нет совпадений по условию(ям)');
        }

        $eventKey = \array_key_first($resultRange);

        if ($eventKey === null) {
            return EventMapper::createFromArray([]);
        }

        $eventData = $this->client->get($eventKey);

        if ($eventData === '(nil)') {
            return EventMapper::createFromArray([]);
        }

        $eventData = \json_decode($eventData, true);

        return EventMapper::createFromArray($eventData);
    }

    public function clearEvents(): void
    {
        $this->client->flushDB();
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
