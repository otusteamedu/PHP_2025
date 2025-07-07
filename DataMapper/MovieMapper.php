<?php

declare(strict_types=1);

namespace DataMapper;

use InvalidArgumentException;

class MovieMapper
{
    public function __construct(private StorageAdapter $adapter){}

    
    public function findById(int $id): Movie
    {
        $result = $this->adapter->find($id);

        if ($result === null) {
            throw new InvalidArgumentException("Movie #$id not found");
        }

        return $this->mapRowToMovie($result);
    }

    /**
     * @return array<Movie>
     */
    public function findAll(): array
    {
        $data = $this->adapter->findAll();
        $movies = [];

        foreach ($data as $row) {
            $movies[] = $this->mapRowToMovie($row);
        }

        return $movies;
    }

    private function mapRowToMovie(array $row): Movie
    {
        return Movie::fromState($row);
    }
} 