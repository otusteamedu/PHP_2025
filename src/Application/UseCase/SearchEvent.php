<?php

namespace App\Application\UseCase;

use App\Domain\Entity\Condition;
use App\Domain\Entity\Event;
use App\Infrastructure\Storage\StorageEventDBInterface;
use Exception;

class SearchEvent
{
    /**
     * @param Condition[] $arrConditions
     * @throws Exception
     */
    public static function execute(StorageEventDBInterface $storage, array $arrConditions): Event
    {
        if (empty($arrConditions)) {
            throw new Exception('Нет условий для поиска');
        }

        return $storage->searchEvent($arrConditions);
    }
}
