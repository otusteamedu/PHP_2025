<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Infrastructure\RabbitMQ\RabbitMQRequestProcessing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

final class RequestProcessingController extends AbstractController
{
    #[OA\Response(
        response: 200,
        description: 'Returns id request processing',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'type', type: 'string'),
                new OA\Property(property: 'status', type: 'boolean'),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Error: Not Found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'type', type: 'string'),
            ]
        )
    )]
    public function index(int $id, RabbitMQRequestProcessing $rabbitMQRequestProcessing): JsonResponse
    {
        $data = $rabbitMQRequestProcessing->getStatus($id);

        return $this->json($data)->setStatusCode($data['type'] === 'success' ? 200 : 404);
    }

    #[OA\Response(
        response: 200,
        description: 'Returns id request processing',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'type', type: 'string'),
            ]
        )
    )]
    public function createRequest(RabbitMQRequestProcessing $rabbitMQRequestProcessing): JsonResponse
    {
        return $this->json($rabbitMQRequestProcessing->publish());
    }
}
