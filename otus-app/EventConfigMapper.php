<?php

namespace App;

use PDO;

class EventConfigMapper extends BaseMapper
{
    public function getEventConfigListByEventId(int $getId)
    {
        $stmt = $this
            ->getPdo()
            ->prepare('SELECT id, config FROM EventConfig WHERE eventId = :eventId');
        $stmt->bindValue(':eventId', $getId, PDO::PARAM_INT);
        $stmt->execute();

        $eventConfigList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultEventConfigList = [];

        foreach ($eventConfigList as $eventConfig) {
            $resultEventConfigList[] = new EventConfig(
                id: $eventConfig['id'],
                config: $eventConfig['config'],
            );
        }

        return $resultEventConfigList;
    }
}
