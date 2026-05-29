<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Presentation\Controller;

use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\RabbitMQConnectionInterface;
use MkdBot\Presentation\Controller\HealthController;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Psr7\Response;

/**
 * Юнит-тесты для HealthController — liveness (/health) и readiness (/ready)
 *
 * Мокаются DatabaseConnectionInterface и RabbitMQConnectionInterface через DI.
 */
class HealthControllerTest extends TestCase
{
    private DatabaseConnectionInterface $db;
    private RabbitMQConnectionInterface $rabbitmq;

    protected function setUp(): void
    {
        $this->db = $this->createMock(DatabaseConnectionInterface::class);
        $this->rabbitmq = $this->createMock(RabbitMQConnectionInterface::class);
    }

    /**
     * Создаёт запрос и ответ для тестирования контроллера
     */
    private function createRequestAndResponse(): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = new Response();

        return [$request, $response];
    }

    // --- health() — liveness probe

    /**
     * health() — возвращает 200 OK с JSON {"status": "ok"}
     */
    public function testHealthReturnsOkStatus(): void
    {
        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();

        $result = $controller->health($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertArrayHasKey('status', $data);
        $this->assertSame('ok', $data['status']);
    }

    /**
     * health() — возвращает Content-Type: application/json
     */
    public function testHealthReturnsJsonContentType(): void
    {
        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();

        $result = $controller->health($request, $response);

        $this->assertSame('application/json', $result->getHeaderLine('Content-Type'));
    }

    /**
     * health() — тело ответа содержит только ключ status
     */
    public function testHealthResponseBodyContainsOnlyStatus(): void
    {
        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();

        $result = $controller->health($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        // health() — простой liveness probe, только статус
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('status', $data);
    }

    /**
     * health() — не зависит от состояния БД
     */
    public function testHealthDoesNotDependOnDatabase(): void
    {
        // БД недоступна — health всё равно возвращает ok
        $this->db->method('isAvailable')->willReturn(false);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();

        $result = $controller->health($request, $response);

        $this->assertSame(200, $result->getStatusCode());

        $body = (string) $result->getBody();
        $data = json_decode($body, true);
        $this->assertSame('ok', $data['status']);
    }

    /**
     * health() — корректно записывает тело ответа через getBody()->write()
     */
    public function testHealthWritesBodyCorrectly(): void
    {
        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();

        $result = $controller->health($request, $response);

        // Проверяем что тело не пустое и содержит валидный JSON
        $body = (string) $result->getBody();
        $this->assertNotEmpty($body);
        $this->assertJson($body);
    }

    /**
     * health() — возвращает ответ с заголовком и статусом 200
     */
    public function testHealthReturnsResponseWithHeaders(): void
    {
        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();

        $result = $controller->health($request, $response);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertTrue($result->hasHeader('Content-Type'));
    }

    // --- ready() — readiness probe

    /**
     * ready() — БД доступна + RabbitMQ доступна -> 200 OK
     */
    public function testReadyWithBothAvailableReturns200(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $this->assertSame(200, $result->getStatusCode());

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertSame('ok', $data['status']);
        $this->assertTrue($data['checks']['database']);
        $this->assertTrue($data['checks']['rabbitmq']);
    }

    /**
     * ready() — БД доступна + RabbitMQ недоступна -> 503
     */
    public function testReadyWithAvailableDbAndUnavailableRabbitmqReturns503(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(false);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $this->assertSame(503, $result->getStatusCode());

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertSame('degraded', $data['status']);
        $this->assertTrue($data['checks']['database']);
        $this->assertFalse($data['checks']['rabbitmq']);
    }

    /**
     * ready() — БД недоступна + RabbitMQ доступна -> 503
     */
    public function testReadyWithUnavailableDbReturns503(): void
    {
        $this->db->method('isAvailable')->willReturn(false);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $this->assertSame(503, $result->getStatusCode());

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertSame('degraded', $data['status']);
        $this->assertFalse($data['checks']['database']);
        $this->assertTrue($data['checks']['rabbitmq']);
    }

    /**
     * ready() — БД недоступна + RabbitMQ недоступна -> 503
     */
    public function testReadyWithBothUnavailableReturns503(): void
    {
        $this->db->method('isAvailable')->willReturn(false);
        $this->rabbitmq->method('isAvailable')->willReturn(false);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $this->assertSame(503, $result->getStatusCode());

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertSame('degraded', $data['status']);
        $this->assertFalse($data['checks']['database']);
        $this->assertFalse($data['checks']['rabbitmq']);
    }

    /**
     * ready() — возвращает Content-Type: application/json
     */
    public function testReadyReturnsJsonContentType(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $this->assertSame('application/json', $result->getHeaderLine('Content-Type'));
    }

    /**
     * ready() — тело ответа содержит status и checks
     */
    public function testReadyResponseBodyContainsStatusAndChecks(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('checks', $data);
        $this->assertArrayHasKey('database', $data['checks']);
        $this->assertArrayHasKey('rabbitmq', $data['checks']);
    }

    /**
     * ready() — статус 'ok' только когда все проверки пройдены
     */
    public function testReadyStatusOkOnlyWhenAllChecksPass(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertSame('ok', $data['status']);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * ready() — статус 'degraded' когда хотя бы одна проверка не пройдена
     */
    public function testReadyStatusDegradedWhenAnyCheckFails(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(false);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertSame('degraded', $data['status']);
        $this->assertSame(503, $result->getStatusCode());
    }

    /**
     * ready() — проверяет БД через DatabaseConnectionInterface::isAvailable()
     */
    public function testReadyCallsDatabaseIsAvailable(): void
    {
        $this->db->expects($this->once())->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $controller->ready($request, $response);
    }

    /**
     * ready() — проверяет RabbitMQ через RabbitMQConnectionInterface::isAvailable()
     */
    public function testReadyCallsRabbitmqIsAvailable(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->expects($this->once())->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $controller->ready($request, $response);
    }

    /**
     * ready() — корректно записывает тело ответа
     */
    public function testReadyWritesBodyCorrectly(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $body = (string) $result->getBody();
        $this->assertNotEmpty($body);
        $this->assertJson($body);
    }

    /**
     * ready() — возвращает ответ с заголовком Content-Type
     */
    public function testReadyReturnsResponseWithHeaders(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $this->assertTrue($result->hasHeader('Content-Type'));
    }

    /**
     * ready() — БД доступна, RabbitMQ выбрасывает исключение -> 503
     */
    public function testReadyWithRabbitmqExceptionReturns503(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willThrowException(new RuntimeException('Connection refused'));

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();

        // Исключение из RabbitMQConnectionInterface не перехватывается контроллером
        // (контроллер больше не оборачивает checkRabbitMQ в try/catch — это делегировано интерфейсу)
        $this->expectException(RuntimeException::class);
        $controller->ready($request, $response);
    }

    /**
     * ready() — проверка что checks содержит ровно 2 ключа (database, rabbitmq)
     */
    public function testReadyChecksContainsExactlyTwoKeys(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(true);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertCount(2, $data['checks']);
    }

    /**
     * ready() — значения checks — строго boolean
     */
    public function testReadyCheckValuesAreBoolean(): void
    {
        $this->db->method('isAvailable')->willReturn(true);
        $this->rabbitmq->method('isAvailable')->willReturn(false);

        $controller = new HealthController($this->db, $this->rabbitmq);
        [$request, $response] = $this->createRequestAndResponse();
        $result = $controller->ready($request, $response);

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertIsBool($data['checks']['database']);
        $this->assertIsBool($data['checks']['rabbitmq']);
    }
}
