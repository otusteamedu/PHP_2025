<?php

namespace App\ES\Query\Filter;

use App\ES\Query\Base\Filter;

class TermFilter implements Filter
{
    private string $field;
    private string $value;

    public function __construct(string $field, string $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function build(): array
    {
        return ['term' => [$this->field => $this->value]];
    }
}