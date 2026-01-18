<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql;

final readonly class Select
{
    /**
     * @param array $select
     */
    public function __construct(private array $select = ['*'])
    {
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return sprintf('SELECT %s', implode(', ', $this->select));
    }
}
