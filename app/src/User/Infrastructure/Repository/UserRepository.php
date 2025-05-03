<?php
declare(strict_types=1);


namespace App\User\Infrastructure\Repository;

use App\Shared\Domain\Repository\Pager;
use App\Shared\Domain\Repository\PaginationResult;
use App\Shared\Infrastructure\Database\Db;
use App\User\Domain\Aggregate\User\User;
use App\User\Domain\Repository\UserFilter;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Infrastructure\Mapper\UserMapper;
use PDO;

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

    private function getCount(string $sql): int
    {
        $statement = $this->db->connection->prepare($sql);
        $statement->execute();

        return $statement->fetchColumn();
    }
}