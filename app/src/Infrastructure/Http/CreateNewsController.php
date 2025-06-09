<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\UseCase\CreateNews\CreateNewsRequest;
use App\Application\UseCase\CreateNews\CreateNewsUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class CreateNewsController extends AbstractController
{
    public function __construct(
        private readonly CreateNewsUseCase $useCase,
    )
    {
    }

    #[Route("/api/v1/news", name: "create_news", methods: ["POST"])]
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
        } catch (Throwable $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
