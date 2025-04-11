<?php

namespace App\Mappers;

use App\Entities\User;
use App\Service\DB;
use App\Service\DBMysql;

class UserMysqlMapper extends Mapper
{
    /** @var string */
    protected string $table = 'users';

    /**
     * @param DBMysql $db
     */
    public function __construct(DBMysql $db) {
        $this->db = $db->table($this->table);
    }

    /**
     * @return DB
     */
    public function getDB(): DB {
        return $this->db;
    }

    /**
     * @param $id
     * @return User|null
     */
    public function findById($id): ?User {
        $user = self::getRecord(User::class, $id);

        if (empty($user)) {
            $result = $this->db->find($id);

            if (empty($result) === false) {
                $user = new User($result);
                self::addRecord($user, $user->id);
            }
        }

        return $user;
    }

    /**
     * @param User $user
     * @return User|bool
     */
    public function create(User $user) {
        $result = $this->db->create($user->toArray());

        if ($result) {
            $user->id = $result;
        } else {
            $user = $result;
        }

        return $user;
    }

    /**
     * @param $id
     * @param User $user
     * @return bool
     */
    public function update($id, User $user): bool {
        return $this->db->update($id, $user->toArray());
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id): bool {
        return $this->db->delete($id);
    }

    /**
     * @return User|null
     */
    public function first(): ?User {
        $user = $this->db->first();

        if (empty($user) === false) {
            $user = new User($user);
            self::addRecord($user, $user->id);
        }

        return $user ?? null;
    }
}