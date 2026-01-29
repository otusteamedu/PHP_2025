<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\DTO;

interface TableRowInterface
{
    public static function getTableHeaders(): array;
    public function toTableRow(): array;
}
