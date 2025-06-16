<?php

namespace Unit;

use App\DTO\EventDTO;
use App\Repositories\RedisRepository;
use PHPUnit\Framework\TestCase;
use RedisException;

class RedisRepositoryTest extends TestCase
{
    /**
     * @throws RedisException
     */
    public function testCreateEvent() {

        $repository = new RedisRepository();

        $event = [
            'name' => 'test',
            'body' => [
                'key1' => 'value1',
            ]
        ];

        $params = [
            'param1' => 1,
            'param2' => 2,
        ];

        $dto = new EventDTO(
            1,
            $event,
            $params
        );

        $repository->createEvent($dto);

        $this->assertTrue(true);
    }

    /**
     * @throws RedisException
     */
    public function testGetEvent() {

        $repository = new RedisRepository();

        $event = [
            'name' => 'test',
            'body' => [
                'key1' => 'value1',
            ]
        ];

        $params = [
            'param1' => 1,
            'param2' => 2,
        ];

        $dto = new EventDTO(
            1,
            $event,
            $params
        );

        $repository->createEvent($dto);

        $event = $repository->getEvent($dto);

        $this->assertNotEmpty($event);
        $this->assertIsArray($event);
        $this->assertEquals('test', $event['name']);
    }


    /**
     * @throws RedisException
     */
    public function testTruncateEvent() {

        $repository = new RedisRepository();

        $event = [
            'name' => 'test',
            'body' => [
                'key1' => 'value1',
            ]
        ];

        $params = [
            'param1' => 1,
            'param2' => 2,
        ];

        $dto = new EventDTO(
            1,
            $event,
            $params
        );

        $repository->createEvent($dto);

        $event = $repository->getEvent($dto);

        $this->assertNotEmpty($event);

        $repository->truncateEvent();

        $event = $repository->getEvent($dto);

        $this->assertEmpty($event);
    }

    /**
     * @throws RedisException
     */
    public function tearDown(): void {
        $repository = new RedisRepository();
        $repository->truncateEvent();
    }
}