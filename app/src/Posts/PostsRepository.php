<?php
declare(strict_types=1);

namespace App\Posts;

use App\Database\Database;

final class PostsRepository
{
    /** @var array<int, Post> */
    private array $identityMap = [];

    public function __construct(private readonly Database $db) {}

    public function add(Post $post): Post
    {
        $sql = <<<SQL
          INSERT INTO posts (user_id, title, body)
          VALUES (:user_id, :title, :body)
          RETURNING id
        SQL;

        $id = (int) $this->db->fetchValue(
            $sql,
            [
                'user_id' => $post->userId,
                'title' => $post->title,
                'body' => $post->body,
            ]
        );
        $postWithId = $post->withId($id);
        $this->identityMap[ $id ] = $postWithId;

        return $postWithId;
    }

    /** @return Post[] */
    public function findAll(?int $userId = null, ?int $limit = null, int $offset = 0): array
    {
        $where = null;
        $limitSql = null;
        $offsetSql = null;
        $bindings = [];

        if ($userId !== null) {
            $where = 'WHERE user_id = :user_id';
            $bindings['user_id'] = $userId;
        }

        if ($limit !== null) {
            $limitSql = 'LIMIT :limit';
            $bindings['limit'] = $limit;
        }

        if ($offset > 0) {
            $offsetSql = 'OFFSET :offset';
            $bindings['offset'] = $offset;
        }

        $sql = <<<SQL
          SELECT
            id,
            user_id,
            title,
            body
          FROM posts
          $where
          ORDER BY id
          $limitSql
          $offsetSql
        SQL;

        $rows = $this->db->yieldArray($sql, $bindings);
        $result = [];
        foreach($rows as $row) {
            $result[] = $this->mapRow($row);
        }

        return $result;
    }

    /**
     * @return Post[]
     */
    public function findByUserId(int $userId, ?int $limit = null, int $offset = 0): array
    {
        return $this->findAll($userId, $limit, $offset);
    }

    private function mapRow(array $row): Post
    {
        $id = (int) $row[ 'id' ];
        if (isset($this->identityMap[ $id ])) {
            return $this->identityMap[ $id ];
        }
        $user = Post::fromArray($row);
        $this->identityMap[ $id ] = $user;

        return $user;
    }
}
