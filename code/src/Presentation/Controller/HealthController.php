<?php

declare(strict_types=1);

namespace MkdBot\Presentation\Controller;

use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\RabbitMQConnectionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Контроллер health-check — liveness (/health) и readiness (/ready)
 */
class HealthController
{
    public function __construct(
        private readonly DatabaseConnectionInterface $db,
        private readonly RabbitMQConnectionInterface $rabbitmq,
    ) {
    }

    /**
     * Liveness: приложение живо
     */
    public function health(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode(['status' => 'ok']));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Readiness: БД + RabbitMQ доступны
     */
    public function ready(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $checks = [
            'database' => $this->db->isAvailable(),
            'rabbitmq' => $this->rabbitmq->isAvailable(),
        ];

        $allOk = !in_array(false, $checks, true);

        $response->getBody()->write(json_encode([
            'status' => $allOk ? 'ok' : 'degraded',
            'checks' => $checks,
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($allOk ? 200 : 503);
    }
}
