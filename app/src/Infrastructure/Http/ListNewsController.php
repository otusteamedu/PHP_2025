<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\NewsContentModifier\AnnotationNewsContentModifier;
use App\Application\NewsContentModifier\DummyNewsContentModifier;
use App\Application\NewsContentModifier\PlainTextNewsContentModifier;
use App\Application\UseCase\ListNews\ListNewsRequest;
use App\Application\UseCase\ListNews\ListNewsResponse;
use App\Application\UseCase\ListNews\ListNewsUseCase;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route("/api/v1/news/", name: "list_news", methods: ["GET"])]
#[OA\Parameter(
    name: 'page',
    description: 'The field used to pagination news list',
    in: 'query',
    schema: new OA\Schema(type: 'int')
)]
#[OA\Response(
    response: 200,
    description: 'Returns the news list',
    content: new OA\JsonContent(
        type: 'array',
        items: new OA\Items(ref: new Model(type: ListNewsResponse::class))
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
class ListNewsController extends AbstractController
{
    public function __construct(
        private readonly ListNewsUseCase $useCase,
    )
    {
    }

    public function __invoke(
        #[MapQueryParameter] int $page = 1,
    ): Response
    {
        try {
            $pageSize = $this->getParameter('app.list_news_page_size');
            $newsContentModifier = new AnnotationNewsContentModifier(
                new PlainTextNewsContentModifier(
                    new DummyNewsContentModifier()
                )
            );

            $request = new ListNewsRequest($page, $pageSize);
            $response = ($this->useCase)($request, $newsContentModifier);

            return $this->json(
                $response,
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
