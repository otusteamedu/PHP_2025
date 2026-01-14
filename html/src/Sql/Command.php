<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql;

final class Command
{
    /**
     * @var Select|null
     */
    private ?Select $select = null;

    /**
     * @var From|null
     */
    private ?From $from = null;

    /**
     * @var Where|null
     */
    private ?Where $where = null;

    /**
     * @var Limit|null
     */
    private ?Limit $limit = null;

    public function __construct()
    {
        $this->select = new Select();
    }

    /**
     * @param Select $select
     *
     * @return Command
     */
    public function select(Select $select): self
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @param From $from
     *
     * @return Command
     */
    public function from(From $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param Where $where
     *
     * @return Command
     */
    public function where(Where $where): self
    {
        $this->where = $where;

        return $this;
    }

    /**
     * @param Limit $limit
     *
     * @return Command
     */
    public function limit(Limit $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return implode(' ', array_filter([
            $this->select?->toSql(),
            $this->from?->toSql(),
            $this->where?->toSql(),
            $this->limit?->toSql(),
        ]));
    }

    /**
     * @return array
     */
    public function toValues(): array
    {
        return $this->where?->toValues() ?? [];
    }
}
