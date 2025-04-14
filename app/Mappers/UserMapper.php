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
     */
    public function create(User $user): User {
        $result = $this->db->create([
            'name' => $user->getName(),
        ]);

        if ($result) {
            $user->setId($result);
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
            $result = $this->db->find($id);

            if (empty($result) === false) {
                $user = new User($result['id'], $result['name']);
                self::addRecord($className, [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                ]);
            }
        } else {
            $user = new User($user['id'], $user['name']);
        }

        return $user;
    }

    /**
     * @return User[]
     */
    public function getAll(): array {
        $users = [];

        foreach ($this->db->get() as $user) {
            $users[] = new User($user['id'], $user['name']);
        }

        return $users;
    }

    public function update(User $user): bool {
        return $this->db->update([
            'id' => $user->getId(),
            'name' => $user->getName(),
        ]);
    }

    public function delete(int $id): bool {
        return $this->db->delete($id);
    }

    /**
     * @throws Exception
     */
    public function first(): ?User {
        $user = $this->db->first();

        if (empty($user) === false) {
            $user = new User($user['id'], $user['name']);
            self::addRecord(User::class, [
                'id' => $user->getId(),
                'name' => $user->getName(),
            ]);
        }

        return $user ?? null;
    }
}