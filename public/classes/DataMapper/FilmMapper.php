<?php declare(strict_types=1);

namespace classes\DataMapper;

use PDO;
use PDOStatement;
use \stdClass as stdClass;

class FilmMapper
{
    private PDO          $pdo;

    private PDOStatement $selectStatement;

    private PDOStatement $selectManyStatement;

    private PDOStatement $insertStatement;

    private PDOStatement $updateStatement;

    private PDOStatement $deleteStatement;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->selectStatement = $pdo->prepare('SELECT * FROM films LIMIT ?');
        $this->selectManyStatement = $pdo->prepare('SELECT * FROM films LIMIT ?');
        $this->insertStatement = $pdo->prepare('INSERT INTO films (title, code, rating) VALUES (?, ?, ?)');
        $this->updateStatement = $pdo->prepare('UPDATE films SET title = ?, code = ?, rating = ? WHERE id = ?');
        $this->deleteStatement = $pdo->prepare('DELETE FROM films WHERE id = ?');
    }

    public function findById(int $id): Film
    {
        $this->selectStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectStatement->execute([$id]);

        $result = $this->selectStatement->fetch();

        return new Film(
            $result['id'],
            $result['title'],
            $result['code'],
            $result['rating'],
        );
    }

    public function getMany(int $limit):stdClass
    {
        $this->selectManyStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectManyStatement->execute([$limit]);

        $arFilms = [];
        while ($row = $this->selectManyStatement->fetch()) {
            $arFilms[] = $row;
        }

        return (object)$arFilms;
    }

    public function insert(array $rawUserData): Film
    {
        $this->insertStatement->execute([
            $rawUserData['title'],
            $rawUserData['code'],
            $rawUserData['rating'],
        ]);

        return new Film(
            (int)$this->pdo->lastInsertId(),
            $rawUserData['title'],
            $rawUserData['code'],
            $rawUserData['rating'],
        );
    }


    public function update(Film $film): bool
    {
        return $this->updateStatement->execute([
            $film->getTitle(),
            $film->getCode(),
            $film->getRating(),
            $film->getId(),
        ]);
    }

    public function delete(Film $film): bool
    {
        return $this->deleteStatement->execute([$film->getId()]);
    }
}