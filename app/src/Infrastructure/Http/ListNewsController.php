<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\NewsContentModifier\AnnotationNewsContentModifier;
use App\Application\NewsContentModifier\DummyNewsContentModifier;
use App\Application\NewsContentModifier\PlainTextNewsContentModifier;
use App\Application\UseCase\ListNews\ListNewsRequest;
use App\Application\UseCase\ListNews\ListNewsUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ListNewsController extends AbstractController
{
    public function __construct(
        private readonly ListNewsUseCase $useCase,
    )
    {
    }

    #[Route("/api/v1/news/", name: "list_news", methods: ["GET"])]
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
