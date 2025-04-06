<?php

declare(strict_types=1);

namespace MyTestApp\DataMapper;

use PDO;
use PDOStatement;

class UserMapper
{
    private PDO          $pdo;

    private PDOStatement $selectAllStatement;
    
    private PDOStatement $selectStatement;

    public $identityMap = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->selectAllStatement = $pdo->prepare(
            'SELECT * FROM users LIMIT :start, :limit '
        );
       
        $this->selectStatement = $pdo->prepare(
            'SELECT * FROM users WHERE id = ?'
        );
    }

    public function findAll($start=0,$limit=2): array
    {
        $this->selectAllStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectAllStatement->bindParam(':start', $start, PDO::PARAM_INT);
        $this->selectAllStatement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $this->selectAllStatement->execute();

        while ($result = $this->selectAllStatement->fetch()) {

            if (!isset($this->identityMap[$result['id']])) {
                $this->identityMap[$result['id']] = new User(
                    $result['id'],
                    $result['name'],
                    $result['email']."(дописан из базы)",
                );
            }
            
        };

        return $this->identityMap;
    }

    public function findById(int $id): User
    {

        if (isset($this->identityMap[$id])) {
            return $this->identityMap[$id];
        }

        $this->selectStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->selectStatement->execute([$id]);

        $result = $this->selectStatement->fetch();

        $user = new User(
            $result['id'],
            $result['name'],
            $result['email'],
        );

        $this->identityMap[$id] = $user;

        return $user;
    }

}
