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
    public function findAll(): array
    {
        $sql = <<<SQL
          SELECT
            id,
            user_id,
            title,
            body
          FROM posts
          ORDER BY id
        SQL;

        $rows = $this->db->fetchAll($sql);

        return array_map(fn(array $row): Post => $this->mapRow($row), $rows);
    }

    /**
     * @return Post[]
     */
    public function findByUserId(int $userId): array
    {
        $sql = <<<SQL
          SELECT
            id,
            user_id,
            title,
            body
          FROM posts 
          WHERE user_id = :0
        SQL;

        $rows = $this->db->fetchAll($sql, [$userId]);
        return array_map(fn(array $row): Post => $this->mapRow($row), $rows);
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
