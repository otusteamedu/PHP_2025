<?php

namespace Unit;

use App\Http\Request;
use Exception;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreate() {
        $request = new Request(
            'POST',
            '/event/get',
            ['key1' => 111],
            ['Accept' => 'application/json']
        );

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals(111, $request->getData()['key1']);
        $this->assertEquals('application/json', $request->getHeaders()['Accept']);
    }

    /**
     * @throws Exception
     */
    public function testCreateUnexpectedRoutePathFail() {
        $this->expectException(Exception::class);

        new Request(
            'POST',
            '/event/undefined',
            [],
            []
        );
    }

    /**
     * @throws Exception
     */
    public function testCreateUnexpectedMethodFail() {
        $this->expectException(Exception::class);

        new Request(
            'undefined',
            '/event/get',
            [],
            []
        );
    }
}