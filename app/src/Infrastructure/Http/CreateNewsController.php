<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\UseCase\CreateNews\CreateNewsRequest;
use App\Application\UseCase\CreateNews\CreateNewsResponse;
use App\Application\UseCase\CreateNews\CreateNewsUseCase;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route("/api/v1/news", name: "create_news", methods: ["POST"])]
#[OA\RequestBody(
    content: new Model(type: CreateNewsRequest::class),
)]
#[OA\Response(
    response: 200,
    description: 'Returns created news ID',
    content: new Model(type: CreateNewsResponse::class),
)]
#[OA\Response(
    response: 400,
    description: 'Bad request or invalid input data',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'error', type: 'string')
        ]
    )
)]
#[OA\Response(
    response: 500,
    description: 'Error while processing request',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'error', type: 'string')
        ]
    )
)]
class CreateNewsController extends AbstractController
{
    public function __construct(
        private readonly CreateNewsUseCase $useCase,
    )
    {
    }

    public function __invoke(
        #[MapRequestPayload] CreateNewsRequest $request
    ): Response
    {
        try {
            $response = ($this->useCase)($request);

            return $this->json(
                $response,
                Response::HTTP_CREATED
            );
        } catch (InvalidArgumentException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
