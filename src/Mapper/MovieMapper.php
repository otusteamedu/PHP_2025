<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Mapper;

use Dinargab\Homework12\Mapper\IdentityMap;
use Dinargab\Homework12\Model\Movie;


class MovieMapper
{

    private \PDO $connection;

    private IdentityMap $identityMap;


    public function __construct(\PDO $connection, $identityMap = null)
    {
        $this->connection = $connection;
        $this->identityMap = $identityMap ?? new IdentityMap();
    }

    /**
     * Save to database or update existing movie object if movieId is not null 
     * 
     * @param Movie $movie Movie object to save
     * @return Movie Saved movie object
     */
    public function save(Movie $movie): Movie
    {
        if (is_null($movie->getId()) || $movie->getId() < 1) {
            $sql = 'insert into movie (title, overview, release_date, duration, rating) 
            values(:title, :overview, :release_date, :duration, :rating)
            returning movie_id;';
            $sth = $this->connection->prepare($sql);
            $sth->execute($this->entityToDb($movie, true));
            $movieId = $sth->fetch(\PDO::FETCH_COLUMN);
        } else {
            $this->update($movie);
            $movieId = $movie->getId();
        }
        $returnMovie = new Movie(
            $movie->getTitle(),
            $movie->getOverview(),
            $movie->getReleaseDate(),
            $movie->getDuration(),
            $movie->getRating(),
            $movieId
        );
        $this->identityMap->setObject($movieId, $returnMovie);
        return $returnMovie;
    }

    /**
     * Get movie by ID
     * 
     * @param int $movieId Movie identifier
     * @return Movie|null
     */
    public function getById(int $movieId): Movie|null
    {
        if ($this->identityMap->hasId($movieId)) {
            return $this->identityMap->getObject($movieId);
        }
        $sql = 'select movie_id, title, duration, overview, release_date, rating 
        from movie
        where movie_id = :movie_id;';
        $sth = $this->connection->prepare($sql);
        $sth->execute(["movie_id" => $movieId]);
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }
        return $this->dbToEntity($result);
    }


    /**
     * Update existing movie
     * 
     * @param Movie $movie Movie object to update
     * @return Movie Updated movie object
     */
    private function update(Movie $movie): Movie
    {
        $sql = 'update movie 
            set title = :title, overview = :overview, release_date = :release_date, duration = :duration, rating = :rating
            where movie_id = :movie_id';
        $sth = $this->connection->prepare($sql);
        $sth->execute($this->entityToDb($movie));

        $this->identityMap->setObject($movie->getId(), $movie);
        return $movie;
    }

    /**
     * Delete movie from database
     * 
     * @param Movie $movie Movie object to delete
     * @return bool True if deletion was successful
     */
    public function delete(Movie $movie): bool
    {
        if (is_null($movie->getId()) || $movie->getId() < 1) {
            return false;
        }
        $sql = 'delete from movie
        where movie_id = :movie_id;';
        $sth = $this->connection->prepare($sql);
        $result = $sth->execute(["movie_id" => $movie->getId()]);
        if ($result) {
            $this->identityMap->deleteObject($movie->getId());
        }
        return $result;
    }

    /**
     * Get movies by title
     * 
     * @param string $title Movie title to search for
     * @return Movie[] Array of movies
     */

    public function getByMovieTitle(string $title): array
    {
        if (empty(trim($title))) {
            return [];
        }
        $sql = 'select movie_id, title, duration, overview, release_date, rating 
            from movie
            where title = :title;';
        $sth = $this->connection->prepare($sql);
        $sth->execute([
            'title' => trim($title)
        ]);
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $this->composeListFromDbValues($result);
    }


    /**
     * Get list of movies with pagination
     * 
     * @param int $limit Number of records to return
     * @param int $offset Number of records to skip
     * @return Movie[] Array of movies
     */
    public function getList(int $limit = 10, int $offset = 0)
    {
        $sql = 'select movie_id, title, duration, overview, release_date, rating
        from movie
        limit :limit
        offset :offset';
        $sth = $this->connection->prepare($sql);
        $sth->execute([
            'limit' => $limit,
            'offset' => $offset
        ]);
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $this->composeListFromDbValues($result);
    }


    /**
     * Compose movie objects from database values
     * 
     * @param array $dbValues Database result array
     * @return Movie[] Array of movie objects
     */

    private function composeListFromDbValues(array $dbValues)
    {
        $resultArray = [];
        foreach ($dbValues as $movieItem) {
            $newMovie = $this->dbToEntity($movieItem);
            if (!$this->identityMap->hasId($newMovie->getId())) {
                $this->identityMap->setObject($newMovie->getId(), $newMovie);
            }
            $resultArray[] = $newMovie;
        }
        return $resultArray;
    }


    private function dbToEntity(array $dbData)
    {
        return new Movie(
                $dbData["title"],
                $dbData["overview"],
                new \DateTime($dbData['release_date']),
                (int) $dbData["duration"],
                (float) $dbData["rating"],
                (int) $dbData["movie_id"]
            );
    }

    private function entityToDb(Movie $movie, $noMovieId = false)
    {
        $returnArray = [
            "title" => $movie->getTitle(),
            "overview" => $movie->getOverview(),
            "release_date" => $movie->getReleaseDate()->format('Y-m-d H:i:s.u'),
            "duration" => $movie->getDuration(),
            "rating" => $movie->getRating(),
        ];
        if (!$noMovieId) {
            $returnArray['movie_id'] = $movie->getId();
        }
        return $returnArray;
    }
}
