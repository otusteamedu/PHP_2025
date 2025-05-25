<?php declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\UseCase\SubmitNews\SubmitNewsRequest;
use App\Application\UseCase\SubmitNews\SubmitNewsUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubmitNewsController extends AbstractController
{
    public function __construct(
        private SubmitNewsUseCase $useCase,
    )
    {
    }

    #[Route('/api/v1/news', name: 'submit_news', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $dto = new SubmitNewsRequest($data['url'] ?? '', $data['title'] ?? '');
            $response = ($this->useCase)($dto);
            return $this->json($response, 201);
        } catch (\Throwable $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
