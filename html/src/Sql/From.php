<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql;

final readonly class From
{
    /**
     * @param string $from
     */
    public function __construct(private string $from)
    {
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return sprintf('FROM %s', $this->from);
    }
}
