<?php

namespace App;

use PDO;

class EventMapper extends BaseMapper
{
    /** @return Event[] */
    public function getEventList(): array
    {
        $stmt = $this->getPdo()->prepare('SELECT id, name FROM Event');
        $stmt->execute();
        $eventList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultList = [];

        foreach ($eventList as $event) {
            $resultList[] = new Event(
                id: $event['id'],
                name: $event['name'],
            );
        }

        return $resultList;
    }
}
