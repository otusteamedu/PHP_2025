<?php

declare(strict_types=1);

namespace Otus\DataMapper\Condition;

use Otus\DataMapper\Factory\CastFactory;

readonly class Equal implements ConditionInterface
{
    /**
     * @param string $field
     * @param mixed $value
     */
    public function __construct(protected string $field, protected mixed $value)
    {
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return $this->field . ' = ?';
    }

    /**
     * @return array
     */
    public function toValues(): array
    {
        return [
            CastFactory::factory($this->value)->getCast(),
        ];
    }
}
