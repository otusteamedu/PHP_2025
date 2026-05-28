<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\PDO;

use App\Application\DTO\PaginationDTO;
use App\Domain\Entity\User;
use App\Domain\Repository;
use PDO;

readonly class PostgresUserRepository implements Repository\UserRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @throws \Exception
     */
    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrateUser($data);
    }

    /**
     * @throws \Exception
     */
    public function findByEmail(string $email): ?User
    {

        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');

        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrateUser($data);
    }

    /**
     * @param int $page
     * @param int $limit
     * @return PaginationDTO
     * @throws \Exception
     */
    public function findAll(int $page = 1, int $limit = 10): PaginationDTO
    {
        $totalStmt = $this->pdo->query('SELECT COUNT(*) FROM users');
        $total = (int) $totalStmt->fetchColumn();

        $offset = ($page - 1) * $limit;
        $stmt = $this->pdo->prepare('SELECT * FROM users ORDER BY id LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        foreach ($usersData as $data) {
            $users[] = $this->hydrateUser($data);
        }

        return new PaginationDTO(
            items: $users,
            total: $total,
            page: $page,
            limit: $limit
        );
    }

    /**
     * @param object $entity
     * @return User
     */
    public function save(object $entity): User
    {
        if (!$entity instanceof User) {
            throw new \InvalidArgumentException('Entity must be an instance of User.');
        }

        if ($entity->getId() !== null) {
            // Update existing user
            $stmt = $this->pdo->prepare(
                'UPDATE users SET name = :name, email = :email, telegram_id = :telegram_id, gender = :gender, weight = :weight, height = :height, born = :born WHERE id = :id'
            );
            $stmt->execute($this->dehydrateUser($entity));
        } else {
            // Insert new user
            $stmt = $this->pdo->prepare(
                'INSERT INTO users (name, email, telegram_id, gender, weight, height, born) VALUES (:name, :email, :telegram_id, :gender, :weight, :height, :born) RETURNING id'
            );
            $stmt->execute($this->dehydrateUser($entity, false));
            $id = $stmt->fetchColumn();
            $entity->setId($id);
        }

        return $entity;
    }

    /**
     * @param object $entity
     */
    public function remove(object $entity): void
    {
        if (!$entity instanceof User || $entity->getId() === null) {
            throw new \InvalidArgumentException('Entity must be an instance of User with a valid ID.');
        }

        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $entity->getId()]);
    }

    /**
     * @throws \Exception
     */
    private function hydrateUser(array $data): User
    {
        return new User(
            (int)$data['id'],
            $data['name'],
            $data['email'],
            $data['telegram_id'],
            $data['gender'],
            (int)$data['weight'],
            (int)$data['height'],
            new \DateTimeImmutable($data['born'])
        );
    }

    private function dehydrateUser(User $user, bool $includeId = true): array
    {
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'telegram_id' => $user->getTelegramId(),
            'gender' => $user->getGender(),
            'weight' => $user->getWeight(),
            'height' => $user->getHeight(),
            'born' => $user->getBorn()->format('Y-m-d'),
        ];

        if ($includeId) {
            $data['id'] = $user->getId();
        }

        return $data;
    }
}
