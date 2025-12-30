<?php

declare(strict_types=1);

namespace Otus\DataMapper\Condition;

interface ConditionInterface
{
    /**
     * @return string
     */
    public function toSql(): string;

    /**
     * @return array
     */
    public function toValues(): array;
}
