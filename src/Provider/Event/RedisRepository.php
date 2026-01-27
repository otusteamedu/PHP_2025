<?php

namespace App\Provider\Event;

use App\Entity\Analytics\Event;
use App\Redis\RedisClient;

class RedisRepository implements IEventRepository
{
    const PREFIX_PARAM = 'event_index_param';

    const PREFIX_EVENT_DATA = 'event:data';

    private $redis;

    public function __construct()
    {
        $this->redis = RedisClient::getInstance()->getClient();
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function add(Event $event): bool
    {
        $redis = $this->redis->multi();
        try {
            foreach ($event->conditions as $param => $value) {
                $redis->sAdd($this->formKeyForIndexParam($param, $value), $event->eventName);
            }
            $redis->hMSet(
                $this->formKeyForEventData($event->eventName),
                [
                    'priority' => $event->priority,
                    'conditions' => json_encode($event->conditions),
                    'event' => $event->eventName,
                    'created_at' => time()
                ]
            );

            $res = $redis->exec();
            if (is_bool($res)) {
                return $res;
            }
            return true;
        } catch (\Throwable $exception) {
            $redis->discard();
            return false;
        }
    }

    /**
     * @param string $eventName
     * @return bool
     */
    public function delete(string $eventName): bool
    {
        $eventsData = $this->redis->hMget($this->formKeyForEventData($eventName), ['conditions']);
        $redis = $this->redis->multi();
        try {
            if ($eventsData['conditions']) {
                $conditions = json_decode($eventsData['conditions'], true);
                if ($conditions) {
                    foreach ($conditions as $param => $value) {
                        $redis->sRem($this->formKeyForIndexParam($param, $value), $eventName);
                    }
                }
            }
            $redis->hDel($this->formKeyForEventData($eventName), 'created_at', 'event', 'conditions', 'priority');
            $res = $redis->exec();
            if (is_bool($res)) {
                return $res;
            }
        } catch (\Throwable $exception) {
            $redis->discard();
            return false;
        }
        return true;
    }

    /**
     * @param array $conditions
     * @return string|null
     */
    public function findEventByConditions(array $conditions): ?string
    {
        $setsKeys = [];
        foreach ($conditions as $param => $value) {
            $key = $this->formKeyForIndexParam($param, $value);
            if (!empty($this->redis->sMembers($key))) {
                $setsKeys[] = $key;
            } else {
                //если не найден хотя бы один параметр, события нет
                return null;
            }
        }

        if (empty($setsKeys)) {
            return null;
        }

        $events = $this->redis->sInter($setsKeys);
        $result = [];
        if ($events) {
            $result = [
                'event' => null,
                'max' => -1,
            ];

            foreach ($events as $key => $eventName) {
                $eventsData = $this->redis->hMget($this->formKeyForEventData($eventName), ['priority']);

                if ($eventsData && $eventsData['priority'] > $result['max']) {
                    $result['max'] = $eventsData['priority'];
                    $result['event'] = $eventName;
                }
            }
        }

       return $result['event'] ?: null;
    }


    /**
     * @param string $event
     * @return mixed|null
     */
    public function getConditionByEvent(string $event): ?array
    {
        $eventsData = $this->redis->hMget($this->formKeyForEventData($event), ['conditions']);

        return $eventsData['conditions'] ? json_decode($eventsData['conditions'], true): null;
    }

    /**
     * @return bool
     */
    public function deleteAll(): bool
    {
        $keys = $this->redis->keys(self::PREFIX_EVENT_DATA . ':*');
        $keys = array_merge($keys, $this->redis->keys(self::PREFIX_PARAM . ':*'));
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
        return true;
    }

    /**
     * @param string $eventName
     * @return string
     */
    private function formKeyForEventData(string $eventName): string
    {
        return self::PREFIX_EVENT_DATA . ":{$eventName}";
    }

    /**
     * @param string $param
     * @param string $value
     * @return string
     */
    public function formKeyForIndexParam(string $param, string $value): string
    {
        return self::PREFIX_PARAM . ":$param:$value";
    }
}