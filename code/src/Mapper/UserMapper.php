<?php

namespace App\Mapper;

use App\Collection\UserCollection;
use App\Entity\User;
use App\IdentityMap\UserIdentityMap;
use PDO;
use RuntimeException;

/**
 * DataMapper для таблицы users.
 */
class UserMapper extends Mapper
{
    public function __construct(PDO $connection, UserIdentityMap $identityMap)
    {
        parent::__construct($connection, $identityMap);
    }

    /**
     * Ищет пользователя по id: сначала в Identity Map, затем в базе данных.
     */
    public function findById(int $id): ?User
    {
        $cachedUser = $this->identityMap->get($id);

        if ($cachedUser instanceof User) {
            return $cachedUser;
        }

        $statement = $this->connection->prepare('SELECT * FROM ' . $this->getTableName() . ' WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        $user = $this->mapRowToObject($row);
        $this->identityMap->set($user);

        return $user;
    }

    /**
     * Возвращает всех пользователей в виде коллекции.
     */
    public function findAll(int $limit = 100, int $offset = 0): UserCollection
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM ' . $this->getTableName() . ' ORDER BY id LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $collection = new UserCollection();

        while ($row = $statement->fetch()) {
            $cachedUser = $this->identityMap->get((int) $row['id']);

            if ($cachedUser instanceof User) {
                $collection->add($cachedUser);
                continue;
            }

            $user = $this->mapRowToObject($row);
            $this->identityMap->set($user);
            $collection->add($user);
        }

        return $collection;
    }

    public function create(string $name, ?int $phone): User
    {
        $name = $this->normalizeName($name);

        $statement = $this->connection->prepare(
            'INSERT INTO ' . $this->getTableName() . ' (name, phone) VALUES (:name, :phone) RETURNING *'
        );
        $statement->execute([
            'name' => $name,
            'phone' => $phone,
        ]);

        $user = $this->mapRowToObject($statement->fetch());
        $this->identityMap->set($user);

        return $user;
    }

    public function update(User $user): void
    {
        $user->setName($this->normalizeName($user->getName()));

        $statement = $this->connection->prepare(
            'UPDATE ' . $this->getTableName() . ' SET name = :name, phone = :phone WHERE id = :id'
        );
        $statement->execute([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'phone' => $user->getPhone(),
        ]);

        if ($statement->rowCount() === 0) {
            throw new RuntimeException('Пользователь с id ' . $user->getId() . ' не найден.');
        }

        $this->identityMap->set($user);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM ' . $this->getTableName() . ' WHERE id = :id');
        $statement->execute(['id' => $id]);

        if ($statement->rowCount() === 0) {
            throw new RuntimeException('Пользователь с id ' . $id . ' не найден.');
        }

        $this->identityMap->delete($id);
    }

    protected function getTableName(): string
    {
        return 'users';
    }

    protected function mapRowToObject(array $row): User
    {
        return new User($row['id'], $row['name'], $row['phone'] === null ? null : $row['phone']);
    }

    private function normalizeName(string $name): string
    {
        return mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8');
    }
}
