<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Factory;

use Otus\Elasticsearch\Entity\Data;

final class DataFactory
{
    /**
     * @param array $data
     *
     * @return Data
     */
    public static function factory(array $data): Data
    {
        return new Data($data);
    }
}
