<?php

namespace App\Tests;

use App\Entities\User;
use App\Mappers\UserMysqlMapper;
use App\Service\DBMysql;
use Dotenv\Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;

class UserMysqlTest extends TestCase
{
    /** @var UserMysqlMapper */
    protected UserMysqlMapper $mapper;

    /**
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();

        Dotenv::createUnsafeImmutable(__DIR__ . '/../../')->load();
        $db = (new DBMysql())->table("users");
        $db->createTableForTest();
        $this->mapper = new UserMysqlMapper($db);
    }

    public function testCreate() {
        $user = new User([
            'name' => base64_encode(random_bytes(10))
        ]);

        $user = $this->mapper->create($user);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testFind() {
        $user = $this->mapper->first();
        $user = $this->mapper->findById($user->id);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @throws Exception
     */
    public function testUpdate() {
        $user = $this->mapper->first();
        $user = new User([
            'id' => $user->id,
            'name' => base64_encode(random_bytes(10))
        ]);

        $result = $this->mapper->update($user->id, $user);

        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testDelete() {
        $user = $this->mapper->first();
        $result = $this->mapper->delete($user->id);

        $this->assertTrue($result);
    }
}