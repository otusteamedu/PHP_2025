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


    public function save(Movie $movie): Movie
    {
        if (is_null($movie->getId()) || $movie->getId() === 0) {
            $sql = 'insert into movie (title, overview, release_date, duration, rating) 
            values(:title, :overview, :release_date, :duration, :rating)
            returning movie_id;';
            $sth = $this->connection->prepare($sql);
            $sth->execute([
                "title" => $movie->getTitle(),
                "overview" => $movie->getOverview(),
                "release_date" => $movie->getReleaseDate()->format('Y-m-d H:i:s.u'),
                "duration" => $movie->getDuration(),
                "rating" => $movie->getRating(),
            ]);
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


    public function getById(int $movieId)
    {
        if ($this->identityMap->hasId($movieId)) {
            return $this->identityMap->getObject($movieId);
        }
        $sql = 'select movie_id, title, duration, overview, release_date, rating 
        from movie
        where movie_id = :movie_id;';
        $sth = $this->connection->prepare($sql);
        $sth->execute(["movie_id" => $movieId]);
        if ($result = $sth->fetch(\PDO::FETCH_ASSOC)) {;
            return new Movie(
                $result["title"],
                $result["overview"],
                new \DateTime($result['release_date']),
                (int) $result["duration"],
                (float) $result["rating"],
                (int) $result["movie_id"]
            );
        }
        return false;
    }



    private function update(Movie $movie): Movie
    {
        $sql = 'update movie 
            set title = :title, overview = :overview, release_date = :release_date, duration = :duration, rating = :rating
            where movie_id = :movie_id';
        $sth = $this->connection->prepare($sql);
        $sth->execute([
            "title" => $movie->getTitle(),
            "overview" => $movie->getOverview(),
            "release_date" => $movie->getReleaseDate()->format('Y-m-d H:i:s.u'),
            "duration" => $movie->getDuration(),
            "rating" => $movie->getRating(),
            "movie_id" => $movie->getId(),
        ]);
        $this->identityMap->setObject($movie->getId(), $movie);
        return $movie;
    }

    public function delete(Movie $movie):bool
    {
        if ($movie->getId() === 0) {
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
            'title' => $title
        ]);
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $this->composeListFromDbValues($result);
    }

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

    private function composeListFromDbValues(array $dbValues)
    {
        $resultArray = [];
        foreach ($dbValues as $movieItem) {
            $newMovie = new Movie(
                $movieItem["title"],
                $movieItem["overview"],
                new \DateTime($movieItem['release_date']),
                (int) $movieItem["duration"],
                (float) $movieItem["rating"],
                (int) $movieItem["movie_id"]
            );
            if (!$this->identityMap->hasId($newMovie->getId())) {
                $this->identityMap->setObject($newMovie->getId(), $newMovie);
            }
            $resultArray[] = $newMovie;
        }
        return $resultArray;
    }
}
