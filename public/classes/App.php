<?php

namespace classes;

use RuntimeException;

use function MongoDB\object;

class App
{

    private NoSqlDBInterface $dbService;


    public function __construct(){
        //$this->dbService = new RedisDBService();
        $this->dbService = new MongoDBService();
    }

    public function run()
    {
        $requestData = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($requestData['action'])) {
            throw new RuntimeException('Action is not defined');
        }


        $response = [];

        if ($requestData['action'] == 'add_event') {
            $this->dbService->addEvent($requestData['event']);
            $response['message'] = 'Event added into storage';
        }
        else if ($requestData['action'] == 'clean_events') {
            $this->dbService->cleanAllEvents();
            $response['message'] = 'All events removed from storage';
        }
        else if ($requestData['action'] == 'get_best_event') {
            $bestEvent = $this->dbService->getMostAppropriateEvent($requestData['params']);

            if (!empty($bestEvent)) {
                $response['best_event'] = $bestEvent;
            } else {
                $response['message'] = 'There are no appropriate events';
            }
        }
        
       return json_encode($response);
    }

}