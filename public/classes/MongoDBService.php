<?php

namespace classes;

use MongoDB\Client as Client;

class MongoDBService implements NoSqlDBInterface
{
    private $client;
    private $db;


    public function __construct(){
        $this->client = new Client("mongodb://mongodb:27017");
        $this->db = $this->client->demo;
    }


    public function addEvent(array $arEvent)
    {
        return $this->db->events->insertOne($arEvent);
    }

    public function cleanAllEvents()
    {
        return $this->db->events->drop();
    }

    public function getAllEvents():array
    {
        $arSavedEvents = [];
        $savedEvents = $this->db->events->find([], ['sort' => ['score' => -1]]);
        foreach ($savedEvents as $event) {
            $arSavedEvents[] = $event['value'];
        };

        return $arSavedEvents;
    }

    public function getMostAppropriateEvent(array $searchParams):array
     {
        $arAppropriateEvents = [];

        $arSavedEvents = $this->getAllEvents();

        foreach ($arSavedEvents as $event) {
            $arEvent = json_decode($event, true);
     
            //событие считает подходящим, если подходит хотя бы одно условие
            foreach ($arEvent['conditions'] as $paramNum => $paramVal) {
                if (isset($searchParams[$paramNum]) && $searchParams[$paramNum] == $paramVal) {
                    $arAppropriateEvents[$arEvent['priority']] = $arEvent;
                    break;
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

}