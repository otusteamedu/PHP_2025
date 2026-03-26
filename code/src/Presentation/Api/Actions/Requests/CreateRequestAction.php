<?php

declare(strict_types=1);

namespace Api\Presentation\Api\Actions\Requests;

use Api\Application\UseCases\CreateRequestUseCase;
use Api\Presentation\Api\Helpers\Json;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

final class CreateRequestAction
{
    public function __construct(
        private CreateRequestUseCase $useCase
    ) {
    }

    #[OA\Post(
        path: '/api/v1/requests',
        summary: 'Создать новый запрос',
        tags: ['Requests'],
        security: [['ApiKeyAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
                content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'Текст запроса')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Запрос создан',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1)
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Неверный API ключ'),
            new OA\Response(response: 400, description: 'Ошибка валидации')
        ]
    )]
    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $content = (string)($data['content'] ?? '');

        try {
            $id = $this->useCase->execute($content);
            return Json::response($response, ['id' => $id]);
        } catch (\InvalidArgumentException $e) {
            return Json::response($response, ['error' => $e->getMessage()], 400);
        }
    }
}
