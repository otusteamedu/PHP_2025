<?php

declare(strict_types=1);

namespace Api\Presentation\Api\Actions\Requests;

use Api\Application\UseCases\GetRequestStatusUseCase;
use Api\Presentation\Api\Helpers\Json;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

final class GetRequestAction
{
    public function __construct(
        private GetRequestStatusUseCase $useCase
    ) {
    }

    #[OA\Get(
        path: '/api/v1/requests/{id}',
        summary: 'Получить статус запроса',
        tags: ['Requests'],
        security: [['ApiKeyAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'ID запроса'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Статус запроса',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'content', type: 'string'),
                        new OA\Property(property: 'created', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'processed', type: 'string', format: 'date-time', nullable: true),
                        new OA\Property(property: 'result', type: 'string', nullable: true)
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Неверный API ключ'),
            new OA\Response(response: 400, description: 'Ошибка валидации'),
            new OA\Response(response: 404, description: 'Запрос не найден')
        ]
    )]
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        if ($id <= 0) {
            return Json::response($response, ['error' => 'ID должен быть положительным числом'], 400);
        }

        $data = $this->useCase->execute($id);

        return $data === null
            ? Json::response($response, ['error' => 'Запрос не найден'], 404)
            : Json::response($response, $data);
    }
}
