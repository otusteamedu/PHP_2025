<?php declare(strict_types=1);

namespace classes\DataMapper;

use PDO;
use PDOStatement;

use classes\DataMapper\Film;

class FilmMapper
{
    private PDO          $pdo;

    private PDOStatement $selectStatement;

    private PDOStatement $insertStatement;

    private PDOStatement $updateStatement;

    private PDOStatement $deleteStatement;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->selectStatement = $pdo->prepare(
            'SELECT * FROM films WHERE id = ?'
        );
        $this->insertStatement = $pdo->prepare(
            'INSERT INTO films (title, code, rating) VALUES (?, ?, ?)'
        );
        $this->updateStatement = $pdo->prepare(
            'UPDATE films SET title = ?, code = ?, rating = ? WHERE id = ?'
        );
        $this->deleteStatement = $pdo->prepare(
            'DELETE FROM films WHERE id = ?'
        );
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


//
//    public function update(User $user): bool
//    {
//        return $this->updateStatement->execute([
//            $user->getFirstName(),
//            $user->getLastName(),
//            $user->getEmail(),
//            $user->getId(),
//        ]);
//    }
//
//    public function delete(User $user): bool
//    {
//        return $this->deleteStatement->execute([$user->getId()]);
//    }
}