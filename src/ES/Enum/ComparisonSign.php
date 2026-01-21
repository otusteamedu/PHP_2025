<?php

namespace App\ES\Enum;

enum ComparisonSign: string
{
    case GreaterThan = '>';
    case GreaterThanOrEqual = '>=';
    case LessThan = '<';
    case LessThanOrEqual = '<=';

    public function toElasticsearchRangeKey(): string
    {
        return match ($this) {
            self::GreaterThan => 'gt',
            self::GreaterThanOrEqual => 'gte',
            self::LessThan => 'lt',
            self::LessThanOrEqual => 'lte',
        };
    }
}

