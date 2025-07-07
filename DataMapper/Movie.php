<?php

declare(strict_types=1);

namespace DataMapper;

class Movie
{
    public static function fromState(array $state): Movie
    {
        $requiredKeys = ['id', 'title', 'year'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $state)) {
                throw new \InvalidArgumentException("Missing required key: $key");
            }
        }

        return new self(
            $state['id'],
            $state['title'],
            $state['year']
        );
    }

    public function __construct(
        private int $_id,
        private string $_title,
        private int $_year
    ) {
    }

    public int $id {
        get => $this->_id;
    }

    public string $title {
        get => $this->_title;
    }

    public int $year {
        get => $this->_year;
    }
}
