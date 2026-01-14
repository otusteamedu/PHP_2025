<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql;

final readonly class Limit
{
    /**
     * @param int $offset
     * @param int $limit
     */
    public function __construct(private int $offset, private int $limit) // todo
    {
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return sprintf('OFFSET %d LIMIT %d', $this->offset, $this->limit); // todo
    }
}
