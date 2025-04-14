<?php

namespace App\Tests;

use App\Entities\User;
use App\Mappers\UserMapper;
use App\Service\DBMysql;
use Dotenv\Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;

class UserPDOTest extends TestCase
{
    protected UserMapper $mapper;

    protected function setUp(): void {
        parent::setUp();

        Dotenv::createUnsafeImmutable(__DIR__ . '/../../')->load();
        $db = (new DBMysql())->table("users");
        $db->createTableForTest();
        $this->mapper = new UserMapper($db);
    }

    public function testCreate() {
        $user = new User(
            null,
            base64_encode(random_bytes(10))
        );

        $user = $this->mapper->create($user);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @throws Exception
     */
    public function testFind() {
        $user = $this->mapper->first();
        $user = $this->mapper->findById($user->getId());

        $this->assertInstanceOf(User::class, $user);
    }

    public function testGetAll() {
        $users = $this->mapper->getAll();

        $this->assertIsArray($users);
        $this->assertInstanceOf(User::class, $users[0]);
    }

    /**
     * @throws Exception
     */
    public function testUpdate() {
        $user = $this->mapper->first();
        $user = new User(
            $user->getId(),
            base64_encode(random_bytes(10))
        );

        $result = $this->mapper->update($user);

        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testDelete() {
        $user = $this->mapper->first();
        $result = $this->mapper->delete($user->getId());

        $this->assertTrue($result);
    }
}