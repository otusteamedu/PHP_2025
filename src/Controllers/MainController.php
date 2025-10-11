<?php

declare(strict_types=1);

namespace Dinargab\Homework11\Controllers;

use Dinargab\Homework11\Model\Event;
use Dinargab\Homework11\Repositories\EventRepositoryInterface;
use Dinargab\Homework11\Response\JsonResponse;
use InvalidArgumentException;

class MainController
{

    private const REQUIRED_ADD_PARAMS = ["event", "conditions", "priority"];
    private const REQUIRED_FIND_PARAMS = ["conditions"];

    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function addEvent()
    {
        if (!$this->validateFields(self::REQUIRED_ADD_PARAMS)) {
            return (new JsonResponse(['message' => 'Required arguments is missing'], 400))->send();
        }
        $event = (string) $_REQUEST["event"];
        $conditions = json_decode($_REQUEST["conditions"], true);
        if (is_null($conditions)) {
            return (new JsonResponse(["message" => "Conditions must be a valid JSON"], 400))->send();
        }
        $priority = (int) $_REQUEST["priority"];


        $event = new Event($event, $conditions, $priority);
        if ($this->eventRepository->add($event)) {
            return (new JsonResponse(['message' => "Event added successfully"], 200))->send(); 
        }
        return (new JsonResponse(['message' => 'Error adding new Event'], 500))->send();
    }


    public function deleteAll()
    {
        $this->eventRepository->deleteAll();
        return (new JsonResponse(['message' => "All events deleted"], 200))->send();
    }


    public function findEvent()
    {
        if (!$this->validateFields(self::REQUIRED_FIND_PARAMS)) {
            return (new JsonResponse(["message" => "Conditions param is missing"], 400))->send();
        }
        $conditions = json_decode($_REQUEST["conditions"], true);
        if (is_null($conditions)) {
            return (new JsonResponse(["message" => "Conditions must be a valid JSON"], 400))->send();
        }
        
        $event = $this->eventRepository->findByConditions($conditions);
        if ($event) {
            return (new JsonResponse(["message" => "Found event", "event" => [
                'name' => $event->getName(),
                'conditions' => $event->getConditions(),
                'priority' => $event->getPriority()
            ]]))->send();
        }
        return (new JsonResponse(["message" => "Event satisfying provided conditions not found"], 404))->send();
    }

    /**
     * Validates that required fields are present and non-empty in the request
     *
     * @param array $fields Array of field names to validate
     * @return bool True if all fields are present and non-empty, false otherwise
     */
    private function validateFields($fields)
    {
        foreach ($fields as $required) {
            if (!isset($_REQUEST[$required]) || empty($_REQUEST[$required])) {
                return false;
            }
        }
        return true;
    }
}