<?php

declare(strict_types=1);

namespace DataMapper;

use InvalidArgumentException;

class MovieMapper
{
    public function __construct(
        private StorageAdapter $adapter,
        private IdentityMap $identityMap = new IdentityMap()
    ) {
    }


    public function findById(int $id): Movie
    {
        $cachedMovie = $this->identityMap->get(Movie::class, $id);
        if ($cachedMovie !== null) {
            return $cachedMovie;
        }

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
        $id = $row['id'];
        
        $cachedMovie = $this->identityMap->get(Movie::class, $id);
        if ($cachedMovie !== null) {
            return $cachedMovie;
        }

        $movie = Movie::fromState($row);
        
        $this->identityMap->set(Movie::class, $id, $movie);
        
        return $movie;
    }

    /**
     * @return array<string, int>
     */
    public function getIdentityMapStats(): array
    {
        return $this->identityMap->getStats();
    }

    
    public function clearIdentityMap(): void
    {
        $this->identityMap->clear();
    }
} 