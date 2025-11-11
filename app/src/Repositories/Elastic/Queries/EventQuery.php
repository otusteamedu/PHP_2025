<?php

declare(strict_types=1);

namespace App\Repositories\Elastic\Queries;

use App\Forms\EventSearch;
use App\Repositories\Elastic\Query;

/**
 * Class EventQuery
 * @package App\Repositories\Elastic\Queries
 */
class EventQuery
{
    /**
     * @param EventSearch $eventSearch
     * @return array
     */
    public static function create(EventSearch $eventSearch): array
    {
        $query = new Query();
        $query->addQueryParam('size', 1);
        $query->addQueryParam('sort', ['priority' => 'desc']);

        foreach ($eventSearch->getConditions() as $condition) {
            $query->addFilterQuery(
                $query->prepareMatch(
                    'conditions.' . $condition->getName(),
                    $condition->getValue()
                )
            );
        }

        return $query->getQuery();
    }
}
