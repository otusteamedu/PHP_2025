<?php
declare(strict_types=1);

class UserDataMapper
{
    private const string USER_CLASS = User::class;

    private \PDO $pdo;

    private IdentityMap $identityMap;

    /**
     * @param PDO $pdo
     * @param IdentityMap $identityMap
     */
    public function __construct(\PDO $pdo, IdentityMap $identityMap)
    {
        $this->pdo = $pdo;
        $this->identityMap = $identityMap;
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        if ($this->identityMap->has(self::USER_CLASS, $id)) {
            return $this->identityMap->get(self::USER_CLASS, $id);
        }

        $stmt = $this->pdo->prepare('SELECT id, email, name, created_at FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $user = $this->mapRowToEntity($row);
        $this->identityMap->set(self::USER_CLASS, $user->getId(), $user);

        return $user;
    }


    /**
     * @param int $limit
     * @param int $offset
     * @return UserCollection
     */
    public function findAll(int $limit = 100, int $offset = 0): UserCollection
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, name, created_at FROM users ORDER BY id LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $collection = new UserCollection();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = (int)$row['id'];

            if ($this->identityMap->has(self::USER_CLASS, $id)) {
                $collection->add($this->identityMap->get(self::USER_CLASS, $id));
                continue;
            }

            $user = $this->mapRowToEntity($row);
            $this->identityMap->set(self::USER_CLASS, $id, $user);
            $collection->add($user);
        }

        return $collection;
    }

    /**
     * @param User $user
     * @return void
     */
    public function insert(User $user): void
    {
        if ($user->getId() !== null) {
            throw new \LogicException('User already persisted');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (email, name, created_at) VALUES (:email, :name, :created_at) RETURNING id'
        );

        $stmt->execute([
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        $id = $stmt->fetchColumn();
        if ($id === false) {
            throw new \RuntimeException('Failed to retrive inserted ID');
        }

        $user->assignId((int)$id);
        $this->identityMap->set(self::USER_CLASS, (int)$id, $user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function update(User $user): void
    {
        $id = $user->getId();
        if ($id === null) {
            throw new \InvalidArgumentException('Cannot update user without ID');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE users SET email = :email, name = :name WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'email' => $user->getEmail(),
            'name' => $user->getName(),
        ]);
    }

    /**
     * @param User $user
     * @return void
     */
    public function delete(User $user): void
    {
        $id = $user->getId();
        if ($id === null) {
            throw new \InvalidArgumentException('Cannot delete user without ID');
        }

        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $this->identityMap->remove(self::USER_CLASS, $id);
    }

    /**
     * @param array $row
     * @return User
     */
    private function mapRowToEntity(array $row): User
    {
        return new User(
            (int)$row['id'],
            $row['name'],
            $row['email'],
            new \DateTimeImmutable($row['created_at'])
        );
    }
}
