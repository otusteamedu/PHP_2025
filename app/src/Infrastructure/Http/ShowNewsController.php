<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\NewsContentModifier\DummyNewsContentModifier;
use App\Application\NewsContentModifier\ReadingTimeNewsContentModifier;
use App\Application\NewsContentModifier\SocialNetworksNewsContentModifier;
use App\Application\ReadingTimeCalculator\ReadingTimeCalculator;
use App\Application\UseCase\ShowNews\ShowNewsRequest;
use App\Application\UseCase\ShowNews\ShowNewsResponse;
use App\Application\UseCase\ShowNews\ShowNewsUseCase;
use App\Domain\Repository\NotFoundException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Throwable;

#[Route("/api/v1/news/{id}", name: "show_news", requirements: ['id' => Requirement::DIGITS], methods: ["GET"])]
#[OA\Response(
    response: 200,
    description: 'Returns a news with specified ID',
    content: new Model(type: ShowNewsResponse::class),
)]
#[OA\Response(
    response: 404,
    description: 'News not found',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'error', type: 'string', example: "News not found")
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
class ShowNewsController extends AbstractController
{
    public function __construct(
        private readonly ShowNewsUseCase $useCase,
    )
    {
    }

    public function __invoke(int $id): Response
    {
        try {
            $newsContentModifier = new SocialNetworksNewsContentModifier(
                new ReadingTimeNewsContentModifier(
                    new DummyNewsContentModifier(),
                    new ReadingTimeCalculator()
                )
            );

            $request = new ShowNewsRequest($id);
            $response = ($this->useCase)($request, $newsContentModifier);

            return $this->json(
                $response,
                Response::HTTP_OK
            );
        } catch (NotFoundException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        } catch (Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
