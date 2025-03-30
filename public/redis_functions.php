<?php

require_once '../vendor/autoload.php';
require_once 'autoload.php';

use Predis\Client as PredisClient;

$redisBD = new PredisClient([
    'host' => 'redis',
    'port' => 6379,
    'connectTimeout' => 2.5,
    'database' => 2,
    'ssl' => ['verify_peer' => false],
]);

//ДОБАВЛЕНИЕ СПИСКА СОБЫТИЙ

//$arEvent1000 = [
//    'priority' => 1000,
//    'conditions' => [
//        'param1' => 1,
//    ],
//    'event' => '::event::'
//];
//
//$arEvent2000 = [
//    'priority' => 2000,
//    'conditions' => [
//        'param1' => 2,
//        'param2' => 2,
//    ],
//    'event' => '::event::'
//];
//
$arEvent3000 = [
   'priority' => 3000,
   'conditions' => [
       'param1' => 1,
       'param2' => 2,
   ],
   'event' => '::event::'
];





//
//pr_debug($arEvent1000);
//
//$event1000 = json_encode($arEvent1000);
//$event2000 = json_encode($arEvent2000);
//$event3000 = json_encode($arEvent3000);
//
//pr_debug($event1000);
//
//
//$redisBD->zadd( 'events', [
//    $event1000 => 1000,
//    $event2000 => 2000,
//    $event3000 => 3000,
//]);


//$searchParams = ['param2' => 2];
//pr_debug($searchParams);
//
////ВЫДАЧА НУЖНОГО СОБЫТИЯ
//$arSavedEvents = $redisBD->zrevrangebyscore('events', 5000, 1000);
//
//$arAppropriateEvents = geAppropriateEvents($arSavedEvents, $searchParams);
//
//pr_debug($arAppropriateEvents);
//if (!empty($arAppropriateEvents)) {
//    $bestEvent = reset($arAppropriateEvents);
//    pr_debug($bestEvent);
//}
//
//function geAppropriateEvents(array $arSavedEvents, array $searchParams):array
//{
//    $arAppropriateEvents = [];
//    foreach ($arSavedEvents as $event) {
//        $arEvent = json_decode($event, true);
//        //событие считает подходящим, если подходит хотя бы одно условие
//        foreach ($arEvent['conditions'] as $paramNum => $paramVal) {
//            if (isset($searchParams[$paramNum]) && $searchParams[$paramNum] == $paramVal) {
//                $arAppropriateEvents[$arEvent['priority']] = $arEvent;
//                break;
//            }
//        }
//    }
//
//    return $arAppropriateEvents;
//}


//echo '23432423432';


//ПОЛУЧЕНИЕ СПИСКА СОБЫТИЙ ПО ПРИОРИТЕТУ
//$arSavedEvents = $redisBD->zrevrangebyscore('events', 5000, 1000);
//pr_debug($arSavedEvents);
//echo '23432423432';


//ОЧИСТКА ВСЕХ ДОСТУПНЫХ СОБЫТИЙ
//$arSavedEvents = $redisBD->zrevrangebyscore('events', 5000, 1000);
//foreach ($arSavedEvents as $event) {
//    //pr_debug($event);
//    $redisBD->zrem('events', $event);
//}


//use classes\App;
//try {
//    $app = new App($commandsNameSpace, $argv);
//    $app->run();
//}
//
//catch (Exception $e) {
//     print_r($e->getMessage());
//}

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