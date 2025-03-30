<?php

namespace classes;

use Predis\Client as PredisClient;

class RedisDBService implements NoSqlDBInterface
{

    private $redisBD;

    private const EVENTS_DB = 'events';
    private const MIN_EVENT_SCORE = 1000;
    private const MAX_EVENT_SCORE = 5000;


    public function __construct() {
        $this->redisBD = new PredisClient([
            'host' => 'redis',
            'port' => 6379,
            'connectTimeout' => 2.5,
            'database' => 2,
            'ssl' => ['verify_peer' => false],
        ]);
    }


    public function addEvent(array $arEvent)
    {
        $eventJson = json_encode($arEvent['conditions']);
        $this->redisBD->zadd( self::EVENTS_DB, [
            $eventJson => $arEvent['priority'],
        ]);
    }

    public function getAllEvents():array
    {
        return $this->redisBD->zrevrangebyscore(self::EVENTS_DB, self::MAX_EVENT_SCORE, self::MIN_EVENT_SCORE);
    }

    public function getMostAppropriateEvent(array $searchParams):array
    {
       $arAppropriateEvents = [];
       $arSavedEvents = $this->getAllEvents();

       if (!empty($arSavedEvents)) {
            foreach ($arSavedEvents as $event) {
                $arEvent = json_decode($event, true);

                //событие считает подходящим, если подходит хотя бы одно условие
                foreach ($arEvent as $paramNum => $paramVal) {
                    if (isset($searchParams[$paramNum]) && $searchParams[$paramNum] == $paramVal) {
                        $arAppropriateEvents[] = $arEvent;
                        break;
                    }
                }
            }
       }
       

       if (!empty($arAppropriateEvents)) {
            $result = reset($arAppropriateEvents);
       } else {
            $result = [];
       }   

    
       return $result;
    }

    public function cleanAllEvents()
    {
        $arSavedEvents = $this->getAllEvents();
        foreach ($arSavedEvents as $event) {
            $this->redisBD->zrem(self::EVENTS_DB, $event);
        }
    }

}