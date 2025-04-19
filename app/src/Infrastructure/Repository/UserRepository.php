<?php
declare(strict_types=1);


namespace App\Infrastructure\Repository;

use App\Domain\Aggregate\User\User;
use App\Domain\Aggregate\User\UserPost;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Database\Db;
use App\Infrastructure\Mapper\UserMapper;

class UserRepository implements UserRepositoryInterface
{
    private Db $db;
    private string $userTable = 'user_user';
    private string $postTable = 'user_post';
    private UserMapper $userMapper;

    public function __construct()
    {
        $this->db = new Db();
        $this->userMapper = new UserMapper();
    }

    public function add(User $user): void
    {
        try {
            $this->db->connection->beginTransaction();
            $exist = $this->checkUserExists($user->id);
            if ($exist) {
                $sql = "UPDATE $this->userTable SET name = :name, email = :email WHERE id = :id;";
            } else {
                $sql = "INSERT INTO $this->userTable (id, email, name) VALUES (:id, :email, :name);";
            }
            $statement = $this->db->connection->prepare($sql);
            $statement->bindValue(':id', $user->id);
            $statement->bindValue(':email', $user->email);
            $statement->bindValue(':name', $user->name);

            if (!$statement->execute()) {
                throw new \Exception('User could not be added into database');
            };
            if (!empty($user->getPosts())) {
                /** @var UserPost $post */
                foreach ($user->getPosts() as $post) {
                    $existedPostIds = $this->getUserPostIds($user->id);
                    if (!in_array($post->getId(), $existedPostIds)) {
                        $sql = "INSERT INTO $this->postTable (id, title, content, owner_id) VALUES (:id, :title, :content, :owner_id);";
                        $statement = $this->db->connection->prepare($sql);
                        $statement->bindValue(':id', $post->getId());
                        $statement->bindValue(':title', $post->getTitle());
                        $statement->bindValue(':content', $post->getContent());
                        $statement->bindValue(':owner_id', $user->id);
                        $statement->execute();
                    }
                }
            }
            $this->db->connection->commit();
        } catch (\Throwable $exception) {
            $this->db->connection->rollBack();
            throw $exception;
        }

    }

    public function get(string $userId): ?User
    {
        $sql = "SELECT * FROM $this->userTable WHERE id = :id;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':id', $userId);
        $statement->execute();

        $result = $statement->fetch();
        if (!$result) {
            return null;
        };

        return $this->userMapper->userMap($result);
    }

    private function checkUserExists(string $userId): bool
    {
        $sql = "SELECT * FROM $this->userTable WHERE id = :id;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':id', $userId);
        $statement->execute();
        $result = $statement->fetch();

        return $result !== false;
    }

    private function getUserPostIds(string $userId): array
    {
        $sql = "SELECT id FROM $this->postTable WHERE owner_id = :id;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':id', $userId);
        $statement->execute();

        return $statement->fetchAll();

    }
}