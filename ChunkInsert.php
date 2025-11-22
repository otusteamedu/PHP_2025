<?php

declare(strict_types=1);

class ChunkInsert
{
    protected const int MAX_ARGS_COUNT = 65_535;
    protected array $values = [];
    protected int $rowsPerInsert;


    public function __construct(
        protected PDO $pdo,
        public readonly string $insertSql,
        public int $columnsCount,
    ) {
        $this->rowsPerInsert = (int)floor(self::MAX_ARGS_COUNT / $this->columnsCount);
    }

    public function addRow(array $row): void
    {
        $this->values[] = $row;
        if (count($this->values) >= $this->rowsPerInsert) {
            $this->insert();
        }
    }

    public function insert(): void
    {
        if (!$this->values)
        {
            return;
        }

        $values = array_map(static function (array $value) {
            return '(' . implode(', ', $value) . ')';
        }, $this->values);
        $values = implode(', ', $values);
        $this->pdo->exec("{$this->insertSql} $values");
        $this->values = [];
    }
}