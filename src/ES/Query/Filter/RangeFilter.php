<?php

namespace App\ES\Query\Filter;

use App\ES\Enum\ComparisonSign;
use App\ES\Query\Base\Filter;

class RangeFilter implements Filter
{
    private string $field;
    private int|float $value;
    private ComparisonSign $sign;
    private array $options;

    public function __construct(string $field, int|float $value, ComparisonSign $sign, array $options = [])
    {
        $this->field = $field;
        $this->value = $value;
        $this->sign = $sign;
        $this->options = $options;
    }

    public function build(): array
    {
        return [
            'range' => [
                $this->field => array_merge([
                    $this->sign->toElasticsearchRangeKey() => $this->value
                ], $this->options)
            ]
        ];
    }

}
