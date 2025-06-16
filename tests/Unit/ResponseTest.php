<?php

namespace Unit;

use App\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testInit() {
        $this->expectOutputString('{"success":true,"message":"Hello World","data":{"key1":"value1"}}');
        $response = new Response(['key1' => 'value1'], 200, "Hello World");
        @$response->init();
    }

    public function testInitBad() {
        $this->expectOutputString('{"success":false,"message":"Hello World"}');
        $response = new Response([], 422, "Hello World");
        @$response->init();
    }
}