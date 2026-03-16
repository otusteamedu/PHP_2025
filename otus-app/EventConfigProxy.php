<?php

namespace App;

class EventConfigProxy
{
    private EventConfigMapper $mapper;

    /** @var null|EventConfig[]  */
    private ?array $eventConfigList = null;

    public function __construct() {
        $this->mapper = new EventConfigMapper();
    }

    public function getEventConfigList(Event $event): array
    {
        if ($this->eventConfigList === null) {
            $this->eventConfigList = $this->mapper->getEventConfigListByEventId($event->getId());
        }

        return $this->eventConfigList;
    }
}
