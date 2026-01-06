<?php

declare(strict_types=1);

namespace Otus\Cache\Factory;

use Otus\Cache\Entity\Search;

final class SearchFactory
{
    /**
     * @param array $data
     *
     * @return Search
     */
    public static function factory(array $data): Search
    {
        $conditions = ConditionsFactory::factory($data);

        return new Search($conditions);
    }
}
