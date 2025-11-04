<?php

namespace Blarkinov\PhpDbCourse\Models\User;

use Blarkinov\PhpDbCourse\Models\Repository\User\UserRepositoryInterface;
use Exception;

class UserMapper
{
    private const TABLE = 'users';

    public function __construct(private UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findByID(int $id): ?User
    {
        $result = $this->repository->select('SELECT * FROM ' . self::TABLE . ' WHERE id=?', [$id]);

        if (empty($result))
            return null;

        return new User(
            $result[0]['id'],
            $result[0]['first_name'],
            $result[0]['last_name'],
            $result[0]['date_birth'],
            $result[0]['gender']
        );
    }

    public function create(): array
    {
        $id = [];
        foreach ($_POST['users'] as $user) {
            $id[] = $this->add($user);
        }
        return $id;
    }

    public function update(int $id): int
    {
        $placeholders = [];
        $parameters = [];
        foreach ($_POST as $column => $value) {
            $placeholders[] = "$column=:$column";
            $parameters[":$column"] = $value;
        }

        $parameters[':id'] = $id;

        $this->repository->update(
            'UPDATE ' . self::TABLE . ' SET ' . implode(',', $placeholders) . ' WHERE id = :id',
            $parameters
        );


        return $id;
    }


    public function getAll(int $offset, int $limit): ?array
    {

        $result = $this->repository->select(
            'SELECT * FROM ' . self::TABLE . " LIMIT :offset,:limit",
            [':offset' => $offset, ':limit' => $limit]
        );

        $total = $this->getCount();

        $result = [
            'rows' => $result,
            'meta' => [
                'total' => $total,
                'offset' => $offset,
                'limit' => $limit,
            ],
        ];

        return $result;
    }

    public function delete(int $id): int
    {
        $this->repository->delete('DELETE FROM ' . self::TABLE . ' WHERE id=?', [$id]);
        return $id;
    }

    private function add(array $data): int
    {
        $id = $this->repository->insert(
            'INSERT INTO ' . self::TABLE . ' (first_name,last_name,date_birth,gender) VALUES (:first_name,:last_name,:date_birth,:gender)',
            [
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':date_birth' => $data['date_birth'],
                ':gender' => $data['gender'],
            ]
        );
        if (!$id)
            throw new Exception('failed create');

        return (int)$id;
    }

    private function getCount()
    {
        $result = $this->repository->select('SELECT count(id) as count FROM ' . self::TABLE);
        return $result[0]['count'];
    }
}
