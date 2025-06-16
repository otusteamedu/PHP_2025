<?php

namespace Unit;


use App\Tasks\GetEventHashTask;
use PHPUnit\Framework\TestCase;

class TasksTest extends TestCase
{
    public function testGetEventHashTask() {
        $task = new GetEventHashTask();

        $params = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $hash = $task->run($params);

        $this->assertEquals('512771dcbee54d00c8f582cf550a592c', $hash);
    }
}