<?php

namespace Arlex2305k\PatternsDb;

class MovieDataMapper implements IMapperRegistry
{
    private \PDO $connection;
    private static IdentityMap $identityMap;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
        if (!isset(self::$identityMap)) {
            self::$identityMap = new IdentityMap();
        }
    }

    public static function getIdentityMap(): IdentityMap
    {
        return self::$identityMap;
    }

    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    public function setConnection(\PDO $connection): void
    {
        $this->connection = $connection;
    }

    public function getById(int $id): ?Movie
    {
        $existingObject = self::$identityMap->get(Movie::class, $id);
        if ($existingObject !== null) {
            return $existingObject;
        }

        $sql = "SELECT id, title, description, duration_minutes, genre, rating FROM movie WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $movie = $this->createMovieFromRow($row);
        self::$identityMap->add(Movie::class, $movie->getId(), $movie);
        return $movie;
    }

    public function getAll(): MovieCollection
    {
        $sql = "SELECT id, title, description, duration_minutes, genre, rating FROM movie ORDER BY id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $movies = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $existingObject = self::$identityMap->get(Movie::class, (int)$row['id']);
            if ($existingObject !== null) {
                $movies[] = $existingObject;
            } else {
                $movie = $this->createMovieFromRow($row);
                self::$identityMap->add(Movie::class, $movie->getId(), $movie);
                $movies[] = $movie;
            }
        }

        return new MovieCollection($movies);
    }

    public function getByOffsetAndLimit(int $offset, int $limit): MovieCollection
    {
        $sql = "SELECT id, title, description, duration_minutes, genre, rating FROM movie ORDER BY id LIMIT :limit OFFSET :offset";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $movies = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $existingObject = self::$identityMap->get(Movie::class, (int)$row['id']);
            if ($existingObject !== null) {
                $movies[] = $existingObject;
            } else {
                $movie = $this->createMovieFromRow($row);
                self::$identityMap->add(Movie::class, $movie->getId(), $movie);
                $movies[] = $movie;
            }
        }

        return new MovieCollection($movies);
    }

    public function save(Movie $movie): Movie
    {
        if ($movie->getId() === null) {
            $sql = "INSERT INTO movie (title, description, duration_minutes, genre, rating) VALUES (:title, :description, :duration_minutes, :genre, :rating) RETURNING id";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':title', $movie->getTitle(), \PDO::PARAM_STR);
            $stmt->bindValue(':description', $movie->getDescription(), \PDO::PARAM_STR);
            $stmt->bindValue(':duration_minutes', $movie->getDurationMinutes(), \PDO::PARAM_INT);
            $stmt->bindValue(':genre', $movie->getGenre(), \PDO::PARAM_STR);
            $stmt->bindValue(':rating', $movie->getRating(), \PDO::PARAM_STR);
            $stmt->execute();
            
            $id = $stmt->fetch(\PDO::FETCH_ASSOC)['id'];
            $movie->setId((int)$id);
			self::$identityMap->add(Movie::class, $movie->getId(), $movie);
        } else {
            $sql = "UPDATE movie SET title = :title, description = :description, duration_minutes = :duration_minutes, genre = :genre, rating = :rating WHERE id = :id";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':id', $movie->getId(), \PDO::PARAM_INT);
            $stmt->bindValue(':title', $movie->getTitle(), \PDO::PARAM_STR);
            $stmt->bindValue(':description', $movie->getDescription(), \PDO::PARAM_STR);
            $stmt->bindValue(':duration_minutes', $movie->getDurationMinutes(), \PDO::PARAM_INT);
            $stmt->bindValue(':genre', $movie->getGenre(), \PDO::PARAM_STR);
            $stmt->bindValue(':rating', $movie->getRating(), \PDO::PARAM_STR); // PARAM_STR для корректной передачи float
            $stmt->execute();
			self::$identityMap->update(Movie::class, $movie->getId(), $movie);
        }
        return $movie;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM movie WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $result = $stmt->execute();
        
        if ($result) {
            $existingObject = self::$identityMap->get(Movie::class, $id);
            if ($existingObject !== null) {
				self::$identityMap->remove(Movie::class, $id);
            }
        }
        
        return $result;
    }

    private function createMovieFromRow(array $row): Movie
    {
        return new Movie(
            $row['title'],
            $row['description'] ?? null,
            (int)$row['duration_minutes'],
            $row['genre'] ?? null,
            $row['rating'] !== null ? (float)$row['rating'] : null,
            (int)$row['id']
        );
    }
}
