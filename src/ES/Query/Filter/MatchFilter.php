<?php

namespace App\ES\Query\Filter;

use App\ES\Query\Base\Filter;

class MatchFilter implements Filter
{
    private string $field;
    private string $value;
    private array $options;

    public function __construct(string $field, string $value, array $options = [])
    {
        $this->field = $field;
        $this->value = $value;
        $this->options = $options;
    }

    public function build(): array
    {
        return [
            'match' => [
                $this->field => array_merge(['query' => $this->value], $this->options)
            ]
        ];
    }
}
