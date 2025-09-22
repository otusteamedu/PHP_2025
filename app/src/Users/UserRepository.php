<?php
declare(strict_types = 1);

namespace App\Users;

use App\Database\Database;
use App\Posts\LazyPostsCollection;
use App\Posts\PostsRepository;

final class UserRepository
{
    /** @var array<int, User> */
    private array $identityMap = [];

    public function __construct(private readonly Database $db, private readonly PostsRepository $posts) {}

    /** @return User[] */
    public function findAll(): array
    {
        $sql = <<<SQL
          SELECT
            id,
            name,
            email
          FROM users
          ORDER BY id
        SQL;

        $rows = $this->db->fetchAll($sql);

        return array_map(fn(array $row): User => $this->mapRow($row), $rows);
    }

    public function findById(int $id): ?User
    {
        if (isset($this->identityMap[ $id ])) {
            return $this->identityMap[ $id ];
        }

        $sql = <<<SQL
          SELECT
            id,
            name,
            email
          FROM users
          WHERE id = :id
        SQL;

        $row = $this->db->fetch($sql, [ 'id' => $id ]);
        if (!$row) {
            return null;
        }

        return $this->mapRow($row);
    }

    public function add(User $user): User
    {
        $sql = <<<SQL
          INSERT INTO users (name, email)
          VALUES (:name, :email)
          RETURNING id
        SQL;

        $id = (int) $this->db->fetchValue(
            $sql,
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );
        $userWithId = $user->withId($id)->withPosts($this->lazyPostsCollection($id));
        $this->identityMap[ $id ] = $userWithId;

        return $userWithId;
    }

    public function update(User $user): void
    {
        $sql = <<<SQL
          UPDATE users
          SET
            name = :name,
            email = :email
          WHERE id = :id
        SQL;

        $this->db->query(
            $sql,
            [
                'name' => $user->name,
                'email' => $user->email,
                'id' => $user->id,
            ]
        );
        $this->identityMap[ $user->id ] = $user;
    }

    public function delete(int $id): void
    {
        $sql = <<<SQL
          DELETE FROM users
          WHERE id = :id
        SQL;

        $this->db->query($sql, [ 'id' => $id ]);
        unset($this->identityMap[ $id ]);
    }

    private function mapRow(array $row): User
    {
        $id = (int) $row[ 'id' ];
        if (isset($this->identityMap[ $id ])) {
            return $this->identityMap[ $id ];
        }
        $user = User::fromArray($row)->withPosts($this->lazyPostsCollection($id));
        $this->identityMap[ $id ] = $user;

        return $user;
    }

    private function lazyPostsCollection(int $id): LazyPostsCollection
    {
        return new LazyPostsCollection(fn() => $this->posts->findByUserId($id));
    }
}
