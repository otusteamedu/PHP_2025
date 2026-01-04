<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\DataReader;

interface ReaderInterface
{
    /**
     * @param int $length
     *
     * @return array
     */
    public function getData(int $length): array;
}
