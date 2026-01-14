<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql;

use Otus\DataMapper\Condition\ConditionInterface;

final readonly class Where
{
    /**
     * @param ConditionInterface $condition
     */
    public function __construct(private ConditionInterface $condition)
    {
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return $this->condition->toSql();
    }

    /**
     * @return array
     */
    public function toValues(): array
    {
        return $this->condition->toValues();
    }
}
