<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql\Condition;

readonly class Condition implements ConditionInterface
{
    /**
     * @var string
     */
    protected string $sql;

    /**
     * @var array
     */
    protected array $values;

    /**
     * @param string $operator
     * @param ConditionInterface ...$condition
     */
    public function __construct(string $operator, ConditionInterface ...$condition)
    {
        $this->sql = implode(' ' . $operator . ' ', array_map(static function (ConditionInterface $condition): string {
            return $condition->toSql();
        }, $condition));

        $this->values = array_merge(...array_map(static function (ConditionInterface $condition): array {
            return $condition->toValues();
        }, $condition));
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return '( ' . $this->sql . ' )';
    }

    /**
     * @return array
     */
    public function toValues(): array
    {
        return $this->values;
    }
}
