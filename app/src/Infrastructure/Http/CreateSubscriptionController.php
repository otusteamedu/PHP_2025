<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\UseCase\CreateSubscription\CreateSubscriptionRequest;
use App\Application\UseCase\CreateSubscription\CreateSubscriptionResponse;
use App\Application\UseCase\CreateSubscription\CreateSubscriptionUseCase;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route("/api/v1/subscriptions", name: "create_subscription", methods: ["POST"])]
#[OA\Response(
    response: 200,
    description: 'Returns a message about successful creation of the subscription',
    content: new Model(type: CreateSubscriptionResponse::class),
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
class CreateSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly CreateSubscriptionUseCase $useCase,
    )
    {
    }

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
