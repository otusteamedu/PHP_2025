<?php

namespace App\Mappers;

use App\Entities\User;
use App\Service\DB;
use Exception;

class UserMapper extends Mapper
{
    protected string $table = 'users';

    public function __construct(DB $db) {
        $this->db = $db->table($this->table);
    }

    /**
     * @param User $user
     * @return User|bool
     * @throws Exception
     */
    public function create(User $user): User {
        $result = $this->db->create([
            'name' => $user->getName(),
        ]);

        if ($result) {
            $user = new User($result, $user->getName());
            self::addRecord($user);
        } else {
            $user = $result;
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function findById(int $id): ?User {
        $className = User::class;
        $user = self::getRecord($className, $id);

        if (empty($user)) {
            $result = $this->db->fetch($id);

            if (empty($result) === false) {
                $user = new User($result['id'], $result['name']);
                self::addRecord($user);
            }
        } else {
            $user = new User($user->getId(), $user->getName());
        }

        return $user;
    }

    /**
     * @return User[]
     */
    public function getAll(): array {
        $users = [];

        foreach ($this->db->fetchAll() as $user) {
            $users[] = new User($user['id'], $user['name']);
        }

        return $users;
    }

    /**
     * @throws Exception
     */
    public function update(User $user): bool {
        $result = $this->db->update([
            'id' => $user->getId(),
            'name' => $user->getName(),
        ]);

        if ($result) {
            self::updateRecord($user);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function delete(int $id): bool {
        $result = $this->db->delete($id);

        if ($result) {
            self::deleteRecord(User::class, $id);
        }

        return $result;
    }

    /**
     * В данный момент нужен для тестов
     *
     * @throws Exception
     */
    public function first(): ?User {
        $user = $this->db->fetchFirst();

        if (empty($user) === false) {
            $user = new User($user['id'], $user['name']);
            self::addRecord($user);
        }

        return $user ?? null;
    }
}