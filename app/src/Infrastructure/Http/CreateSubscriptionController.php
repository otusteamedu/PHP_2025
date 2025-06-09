<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\UseCase\CreateSubscription\CreateSubscriptionRequest;
use App\Application\UseCase\CreateSubscription\CreateSubscriptionUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class CreateSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly CreateSubscriptionUseCase $useCase,
    )
    {
    }

    #[Route("/api/v1/subscriptions", name: "create_subscription", methods: ["POST"])]
    public function __invoke(
        #[MapRequestPayload] CreateSubscriptionRequest $request
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
