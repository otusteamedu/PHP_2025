<?php
declare(strict_types=1);


namespace App\Infrastructure\Repository;

use App\Domain\Aggregate\User\User;
use App\Domain\Aggregate\User\UserPost;
use App\Domain\Repository\Pager;
use App\Domain\Repository\PaginationResult;
use App\Domain\Repository\UserFilter;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Database\Db;
use App\Infrastructure\Mapper\UserMapper;
use App\Infrastructure\Mapper\UserPostMapper;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private Db $db;
    private string $userTable = 'user_user';
    private string $postTable = 'user_post';
    private UserMapper $userMapper;
    private UserPostMapper $userPostMapper;

    public function __construct()
    {
        $this->db = new Db();
        $this->userMapper = new UserMapper();
        $this->userPostMapper = new UserPostMapper();
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
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        };
        $user = $this->userMapper->userMap($result);
        $this->setPostReference($user);

        return $user;
    }

    public function getByFilter(UserFilter $filter): ?PaginationResult
    {
        $pager = $filter->pager;
        if (!$pager) {
            $pager = Pager::fromPage(null, null);
        }
        $sql = "SELECT * FROM $this->userTable LIMIT :limit OFFSET :offset;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':limit', $pager->getLimit());
        $statement->bindValue(':offset', $pager->getOffset());
        $statement->execute();
        $users = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $user = $this->userMapper->userMap($row);
            $user->setRepoRef($this->getPostReference());
            $users[] = $user;

        };
        $count = $this->getCount("SELECT count(*) FROM $this->userTable;");

        return new PaginationResult($users, $count);
    }

    private function checkUserExists(string $userId): bool
    {
        $sql = "SELECT * FROM $this->userTable WHERE id = :id;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':id', $userId);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result !== false;
    }

    private function getUserPostIds(string $userId): array
    {
        $sql = "SELECT id FROM $this->postTable WHERE owner_id = :id;";
        $statement = $this->db->connection->prepare($sql);
        $statement->bindValue(':id', $userId);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }

    private function getCount(string $sql): int
    {
        $statement = $this->db->connection->prepare($sql);
        $statement->execute();

        return $statement->fetchColumn();
    }

    private function getPostReference(): \Closure
    {
        return function (User $user) {
            $sql = "SELECT * FROM $this->postTable WHERE owner_id = :id;";
            $statement = $this->db->connection->prepare($sql);
            $statement->bindValue(':id', $user->id);
            $statement->execute();

            $posts = [];
            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $post) {
                $posts[] = $this->userPostMapper->userPostMap($user, $post);
            }

            return $posts;
        };
    }
}