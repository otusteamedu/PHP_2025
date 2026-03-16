<?php

namespace Crowley\App\Presenters\Controllers;

use Crowley\App\Application\Services\EventStorage;

class HomeController extends BaseController
{

    public function index() {

        $this->render("home/index");
    }

    public function addEvent () {

        $priority = $_REQUEST["priority"];
        $conditions = json_decode($_REQUEST["conditions"], true);
        $event = json_decode($_REQUEST["event"], true);

        $storage = new EventStorage();
        $storage->addEvent($priority, $conditions, $event);

    }

    public function getEvents() {
        $events = new EventStorage();
        $eventsList = $events->getEvents();

        $this->render("home/events", compact('eventsList'));
    }

    public function getEvent() {

        $event = null;

        if (isset($_REQUEST["conditions"]) && !empty($_REQUEST["conditions"])) {
            $conditions = json_decode($_REQUEST["conditions"], true);

            $events = new EventStorage();
            $event = $events->getEvent($conditions);
        }

        $this->render("home/event", compact('event'));
    }

    public function clearEvents() {
        $storage = new EventStorage();
        $storage->clearEvents();
    }

}