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
            $arSavedEvents[] = $event['conditions'];
        };

        return $arSavedEvents;
    }

    public function getMostAppropriateEvent(array $searchParams):array
     {
        $arAppropriateEvents = [];

        $arSavedEvents = $this->getAllEvents();

        foreach ($arSavedEvents as $arEvent) {
            //событие считает подходящим, если подходит хотя бы одно условие
            foreach ($arEvent as $paramNum => $paramVal) {
                if (isset($searchParams[$paramNum]) && $searchParams[$paramNum] == $paramVal) {
                    $arAppropriateEvents[] = $arEvent;
                    break;
                }
            }
        }

        if (!empty($arAppropriateEvents)) {
            $result = (array)reset($arAppropriateEvents);
        } else {
            $result = [];
        }   
     
        return $result;
    }
}