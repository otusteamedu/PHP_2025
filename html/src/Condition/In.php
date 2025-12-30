<?php

declare(strict_types=1);

namespace Otus\DataMapper\Condition;

use Otus\DataMapper\Factory\CastFactory;

readonly class In implements ConditionInterface
{
    /**
     * @param string $field
     * @param array $in
     */
    public function __construct(protected string $field, protected array $in)
    {
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        $in = array_fill(0, count($this->in), '?');

        return $this->field . ' IN (' . implode(', ', $in) . ')';
    }

    /**
     * @return array
     */
    public function toValues(): array
    {
        return array_map(static function (mixed $value): mixed {
            return CastFactory::factory($value)->getCast();
        }, $this->in);
    }
}
