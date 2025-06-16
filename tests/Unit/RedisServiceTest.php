<?php

namespace Unit;

use App\Services\RedisService;
use PHPUnit\Framework\TestCase;
use Redis;

class RedisServiceTest extends TestCase
{
    public function testConnection()
    {
        $result = new RedisService();
        $this->assertInstanceOf(Redis::class, $result->client);
    }
}