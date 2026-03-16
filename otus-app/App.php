<?php

namespace App;

class App
{
    public function __construct(
        private EventMapper $eventMapper,
    ){
    }

    public function run(array $idList): string
    {
        $resultMessage = '';

        foreach ($this->eventMapper->getEventList() as $event) {
            $resultMessage .= $event->getName();

            if (in_array($event->getId(), $idList, true)) {
                $eventConfigList = $event->getEventConfigList();

                $resultEventConfigList = [];

                foreach ($eventConfigList as $eventConfig) {
                    $resultEventConfigList[] = $eventConfig->getConfig();
                }

                $resultMessage .= ' Config: ' . implode(',', $resultEventConfigList);
            }

            $resultMessage .= "<br>";
        }

        return $resultMessage;
    }
}