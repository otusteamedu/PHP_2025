<?php
namespace Aovchinnikova\Hw15\Tests\Functional;

use PHPUnit\Framework\TestCase;

class EmailApiTest extends TestCase
{
    public function testApiEndpoint()
    {
        $payload = json_encode(['emails' => ['test@example.com']]);
        $ch = curl_init('http://nginx/');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Host: mysite.local',
            'Content-Length: ' . strlen($payload)
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode, "HTTP status code should be 200");
        $data = json_decode($response, true);
        $this->assertArrayHasKey('test@example.com', $data);
    }
}
