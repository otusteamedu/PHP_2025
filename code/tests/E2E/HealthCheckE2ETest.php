<?php

declare(strict_types=1);

namespace MkdBot\Tests\E2E;

/**
 * E2E-тесты health-эндпоинтов
 *
 * Проверяет что /health и /ready доступны через сервер
 * и возвращают корректные HTTP-статусы и JSON-структуру.
 *
 * @group e2e
 */
class HealthCheckE2ETest extends BaseE2ETest
{
    /**
     * GET /health -> 200, JSON {"status":"ok"}
     */
    public function testHealthEndpointReturnsOk(): void
    {
        $response = $this->httpClient()->get('/health');
        $status = $response->getStatusCode();
        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $status, 'Эндпоинт /health должен возвращать 200');
        $this->assertIsArray($body, 'Ответ /health должен быть JSON-объектом');
        $this->assertArrayHasKey('status', $body, 'Ответ /health должен содержать ключ status');
        $this->assertEquals('ok', $body['status'], 'status должен быть "ok"');
    }

    /**
     * GET /ready -> 200, проверка что database и rabbitmq = true
     */
    public function testReadyEndpointReturnsOk(): void
    {
        $response = $this->httpClient()->get('/ready');
        $status = $response->getStatusCode();
        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $status, 'Эндпоинт /ready должен возвращать 200 при доступных сервисах');
        $this->assertIsArray($body, 'Ответ /ready должен быть JSON-объектом');
        $this->assertArrayHasKey('status', $body, 'Ответ /ready должен содержать ключ status');
        $this->assertEquals('ok', $body['status'], 'status должен быть "ok" при доступных сервисах');
        $this->assertArrayHasKey('checks', $body, 'Ответ /ready должен содержать ключ checks');
        $this->assertTrue($body['checks']['database'] ?? false, 'database должен быть true');
        $this->assertTrue($body['checks']['rabbitmq'] ?? false, 'rabbitmq должен быть true');
    }
}
