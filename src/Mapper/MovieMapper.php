<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Mapper;

use Dinargab\Homework12\Mapper\IdentityMap;
use Dinargab\Homework12\Model\Movie;
use InvalidArgumentException;

class MovieMapper
{

    private \PDO $connection;

    private IdentityMap $identityMap;


    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
        $this->identityMap = new IdentityMap();
    }

    /**
     * Save to database or update existing movie object if movieId is not null 
     * 
     * @param Movie $movie Movie object to save
     * @return Movie Saved movie object
     */
    public function save(Movie $movie): Movie
    {
        if (is_null($movie->getId())) {
            $returnMovie = $this->insert($movie);
        } else {
            $returnMovie = $this->update($movie);
        }
        $this->identityMap->setObject($returnMovie, $returnMovie->getId());
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
        if ($this->identityMap->hasId(Movie::class, $movieId)) {
            return $this->identityMap->getObject(Movie::class, $movieId);
        }
        $sql = 'SELECT movie_id, title, duration, overview, release_date, rating FROM movie WHERE movie_id = :movie_id;';
        $sth = $this->connection->prepare($sql);
        $sth->execute(["movie_id" => $movieId]);
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }
        return $this->dbToEntity($result);
    }
    /**
     * Insert new movie into DB
     * 
     * @param Movie $movie Movie object to insert
     * @return Movie inserted movie object
     */

    private function insert(Movie $movie): Movie
    {
        $sql = 'INSERT INTO movie (title, overview, release_date, duration, rating) VALUES(:title, :overview, :release_date, :duration, :rating);';
        $sth = $this->connection->prepare($sql);
        $sth->execute($this->entityToDb($movie, true));

        return new Movie(
            $movie->getTitle(),
            $movie->getOverview(),
            $movie->getReleaseDate(),
            $movie->getDuration(),
            $movie->getRating(),
            (int) $this->connection->lastInsertId()
        );
    }


    /**
     * Update existing movie
     * 
     * @param Movie $movie Movie object to update
     * @return Movie Updated movie object
     */
    private function update(Movie $movie): Movie
    {
        $sql = 'UPDATE movie SET title = :title, overview = :overview, release_date = :release_date, duration = :duration, rating = :rating WHERE movie_id = :movie_id';
        $sth = $this->connection->prepare($sql);
        $sth->execute($this->entityToDb($movie));
        return $movie;
    }

    /**
     * Delete movie from database
     * 
     * @param Movie|int $movie Movie object to delete or id of movie to delete
     * @return bool True if deletion was successful
     */
    public function delete(Movie|int $movie): bool
    {
        $movieId = $movie;
        if (is_object($movie) && get_class($movie) == Movie::class) {
            $movieId = $movie->getId();
        }
        if (is_null($movieId)) {
            return false;
        }
        $sql = 'DELETE from movie WHERE movie_id = :movie_id;';
        $sth = $this->connection->prepare($sql);
        $result = $sth->execute(["movie_id" => $movieId]);
        if ($result) {
            $this->identityMap->deleteObject(Movie::class, $movieId);
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
        $sql = 'SELECT movie_id, title, duration, overview, release_date, rating FROM movie WHERE title = :title;';
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
        if ($limit < 1) {
            throw new InvalidArgumentException("Limit must be greater than zero");
        }
        if ($offset < 0) {
            throw new InvalidArgumentException("Offset can not be negative");
        }
        $sql = 'SELECT movie_id, title, duration, overview, release_date, rating FROM movie LIMIT :limit OFFSET :offset;';
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
            if (!$this->identityMap->hasId(Movie::class, $movieItem['movie_id'])) {
                $newMovie = $this->dbToEntity($movieItem);
                $this->identityMap->setObject($newMovie, $newMovie->getId());
            }
            $resultArray[] = $this->identityMap->getObject(Movie::class, $movieItem['movie_id']);
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
