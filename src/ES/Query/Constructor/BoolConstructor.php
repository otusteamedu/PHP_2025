<?php

namespace App\ES\Query\Constructor;

use App\ES\Query\Base\QueryComponent;

class BoolConstructor implements QueryComponent
{
    private array $must = [];
    private array $filter = [];
    private array $should = [];
    private array $mustNot = [];
    private ?int $minimumShouldMatch = null;

    public function must(QueryComponent $component): self
    {
        $this->must[] = $component;
        return $this;
    }

    public function filter(QueryComponent $component): self
    {
        $this->filter[] = $component;
        return $this;
    }

    public function should(QueryComponent $component): self
    {
        $this->should[] = $component;
        return $this;
    }

    public function mustNot(QueryComponent $component): self
    {
        $this->mustNot[] = $component;
        return $this;
    }

    public function minimumShouldMatch(int $value): self
    {
        $this->minimumShouldMatch = $value;
        return $this;
    }

    public function build(): array
    {
        $result = [];

        if (!empty($this->must)) {
            $result['must'] = array_map(fn($c) => $c->build(), $this->must);
        }

        if (!empty($this->filter)) {
            $result['filter'] = array_map(fn($c) => $c->build(), $this->filter);
        }

        if (!empty($this->should)) {
            $result['should'] = array_map(fn($c) => $c->build(), $this->should);
        }

        if (!empty($this->mustNot)) {
            $result['must_not'] = array_map(fn($c) => $c->build(), $this->mustNot);
        }

        if ($this->minimumShouldMatch !== null) {
            $result['minimum_should_match'] = $this->minimumShouldMatch;
        }

        return ['bool' => $result];
    }
}