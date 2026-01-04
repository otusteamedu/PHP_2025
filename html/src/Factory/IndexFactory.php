<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Factory;

use Otus\Elasticsearch\Entity\Index;

final class IndexFactory
{
    /**
     * @param array $data
     *
     * @return Index
     */
    public static function factory(array $data): Index
    {
        $operation = array_key_first($data);

        [
            $operation => [
                '_index' => $index,
                '_id' => $id,
            ]
        ] = $data;

        return new Index($operation, $index, $id);
    }
}
