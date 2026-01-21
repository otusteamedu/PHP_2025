<?php

namespace App\ES\Query\Constructor;

use App\ES\Query\Base\QueryComponent;

class NestedConstructor implements QueryComponent
{
    private string $path;

    private ?QueryComponent $query = null;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function add(QueryComponent $component): self
    {
        $this->query = $component;
        return $this;
    }

    public function build(): array
    {
        return [
            'nested' => [
                'path'  => $this->path,
                'query' => $this->query?->build() ?? [],
            ],
        ];
    }
}
