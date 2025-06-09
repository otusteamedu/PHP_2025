<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\NewsContentModifier\DummyNewsContentModifier;
use App\Application\NewsContentModifier\ReadingTimeNewsContentModifier;
use App\Application\NewsContentModifier\SocialNetworksNewsContentModifier;
use App\Application\ReadingTimeCalculator\ReadingTimeCalculator;
use App\Application\UseCase\ShowNews\ShowNewsRequest;
use App\Application\UseCase\ShowNews\ShowNewsUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Throwable;

class ShowNewsController extends AbstractController
{
    public function __construct(
        private readonly ShowNewsUseCase $useCase,
    )
    {
    }

    #[Route("/api/v1/news/{id}", name: "show_news", requirements: ['id' => Requirement::DIGITS], methods: ["GET"])]
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
        } catch (Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
