<?php

namespace Tests\Unit\Core\Exceptions;

use App\Core\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;

class NotFoundExceptionTest extends TestCase
{
    public function testException()
    {
        $exception = new NotFoundException('Test message', 404);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
    }
}