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

function pr_debug($var)
{
    //if ($_REQUEST['deb'] == 'Y') {
    $bt = debug_backtrace();
    $bt = $bt[0];
    ?>
    <div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>
        <div style='padding:3px 5px; background:#99CCFF; font-weight:bold;'>File: <?= $bt["file"] ?>
            [<?= $bt["line"] ?>]
        </div>
        <?
        if ($var === 0) {
            echo '<pre>пусто</pre>';
            var_dump($var);
        } else {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }
        ?>
    </div>
    <?
    //exit();
    //}
}