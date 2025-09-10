<?php
namespace App\Mapper;

use App\Base\Db;
use App\Entity\Movie;
use App\IdentityMap;
use DateTime;
use PDO;

class MovieMapper
{
    private array $validators;
    private array $convertersFromDb;
    private string $tableName = 'movie';

    private PDO $pdo;
    private \PDOStatement $pdoSelect;
    private \PDOStatement $pdoSelectList;
    private \PDOStatement $pdoInsert;
    private \PDOStatement $pdoUpdate;
    private \PDOStatement $pdoDelete;

    public function __construct()
    {
        $this->validators = [
            'id' => fn($value) => is_numeric($value),
            'name' => fn($value) => is_string($value) && $value !== '',
            'release_date' => function ($value) {
                $date = DateTime::createFromFormat('Y-m-d', $value);
                return is_string($value) && $date->format('Y-m-d') === $value;
            },
            'duration' => function ($value) {
                return preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $value);
            },
            'description' => fn($value) => is_string($value),
        ];

        $this->convertersFromDb = [
            'release_date' => function ($value) {
                 return  DateTime::createFromFormat('Y-m-d', $value);
            },
            'duration' => function ($value) {
                $time = DateTime::createFromFormat('H:i:s', $value);
                $h = $time->format('H') * 60 * 60;
                $m = $time->format('i') * 60;
                $s = $time->format('s');
                return (int)$h + (int)$m + (int)$s;
            }
        ];

        $this->pdo = DB::getInstance()->getConnection();
        $this->pdoSelect = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1");
        $this->pdoSelectList = $this->pdo->prepare("SELECT * FROM {$this->tableName} LIMIT :limit OFFSET :offset");
        $this->pdoDelete = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE id = :id");
        $this->pdoInsert = $this->pdo->prepare("INSERT INTO {$this->tableName}
                                                        (name, description, release_date, duration) 
                                                        VALUES (:name, :description, :release_date, :duration)");

        $this->pdoUpdate = $this->pdo->prepare("UPDATE {$this->tableName}    
                                                        SET name=:name, description=:description, release_date=:release_date,
                                                        duration=:duration WHERE id=:id");

    }

    /**
     * @param Movie $movie
     * @return bool
     */
        public function add(Movie $movie): bool
        {
            $data = $this->convertInDb($movie);

            $this->pdoInsert->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $this->pdoInsert->bindValue(':description', $data['description'], PDO::PARAM_STR);
            $this->pdoInsert->bindValue(':release_date', $data['release_date'], PDO::PARAM_STR);
            $this->pdoInsert->bindValue(':duration', $data['duration'], PDO::PARAM_STR);
            $result = $this->pdoInsert->execute();
            if ($result) {
                IdentityMap::setObject($this->pdo->lastInsertId(), $this->tableName, $movie);
            }

            return $this->pdoInsert->execute();
        }

    /**
     * @param Movie $movie
     * @return bool
     */
    public function update(Movie $movie): bool
    {
        $data = $this->convertInDb($movie);
        $this->pdoUpdate->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $this->pdoUpdate->bindValue(':description', $data['description'], PDO::PARAM_STR);
        $this->pdoUpdate->bindValue(':release_date', $data['release_date'], PDO::PARAM_STR);
        $this->pdoUpdate->bindValue(':duration', $data['duration'], PDO::PARAM_STR);
        $this->pdoUpdate->bindValue(':id', $movie->getId(), PDO::PARAM_INT);

        if (empty(IdentityMap::getObject($movie->getId(), $this->tableName))) {
            IdentityMap::setObject($movie->getId(), $this->tableName, $movie);
        }

        return $this->pdoUpdate->execute();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->pdoDelete->bindValue(':id', $id, PDO::PARAM_INT);
        if (!empty(IdentityMap::getObject($id, $this->tableName))) {
            IdentityMap::deleteObject($id, $this->tableName);
        }
        return $this->pdoDelete->execute();
    }

    /**
     * @param int $id
     * @return object|Movie|mixed|null
     */
    public function getObjectById(int $id): object
    {
        if (!empty(IdentityMap::getObject($id, $this->tableName))) {
            return IdentityMap::getObject($id, $this->tableName);
        }
        $this->pdoSelect->bindValue(1, $id, PDO::PARAM_INT);
        $this->pdoSelect->execute();
        $resultDb = $this->pdoSelect->fetch(PDO::FETCH_ASSOC);
        $this->validate($resultDb, (int)$resultDb['id']);
        $this->convertFromDb($resultDb);


        return new Movie($resultDb['name'],
            $resultDb['release_date'],
            $resultDb['duration'],
            $resultDb['description'],
            $resultDb['id']
        );
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getList(int $limit, int $offset): array
    {
        $this->pdoSelectList->bindValue(':limit', $limit, PDO::PARAM_INT);
        $this->pdoSelectList->bindValue(':offset', $offset, PDO::PARAM_INT);
        $this->pdoSelectList->execute();
        $result = [];

        foreach ($this->pdoSelectList->fetchAll(PDO::FETCH_ASSOC) as $movie) {
            $this->convertFromDb($movie);
            $result[] = new Movie($movie['name'], $movie['release_date'], $movie['duration'], $movie['description'], $movie['id']);
        }

        return $result;
    }


    /**
     * @param Movie $movie
     * @return array
     */
    private function convertInDb(Movie $movie): array
    {
       return [
           'name' => $movie->getName(),
           'duration' => $this->convertDurationForDb($movie->getDuration()),
           'description' => $movie->getDescription(),
           'release_date' => $movie->getDateRelease()->format('Y-m-d'),
        ];
    }

    /**
     * Конвертация длительности фильма для бд
     *
     * @param int $duration
     * @return string
     */
    private function convertDurationForDb(int $duration): string
    {
        if ($duration < 60) {
            return "$duration secs";
        }
        $minuts = floor($duration / 60);
        $sec = $duration % 60;

        if ($minuts < 60) {
            return "$minuts mins $sec secs";
        }
        $hour = floor($minuts / 60);
        $minuts = $minuts % 60;

        return "$hour hours $minuts mins $sec secs";
    }

    /**
     * Конвертация для объекта
     * @param array $data
     * @return void
     */
    private function convertFromDb(array &$data): void
    {
        foreach ($this->convertersFromDb as $name => $converter) {
            if (isset($data[$name]) && is_callable($converter)) {
                $data[$name] = $converter($data[$name]);
            }
        }
    }

    /**
     * Проверяет тип данных, которые пришли из БД
     *
     * @param array $data
     * @param int $movieId
     * @return void
     */
    private function validate(array $data, int $movieId): void
    {
        foreach ($this->validators as $name => $validator) {
            if (isset($data[$name]) && !$validator($data[$name])) {
                throw new \TypeError(
                    "Invalid value for field {$name}: " . var_export($name, true) .
                    " for movie ID: {$movieId}"
                );
            }
        }
    }

}