<?php declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\UseCase\FindAllNews\FindAllNewsUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FindAllNewsController extends AbstractController
{
    public function __construct(
        private FindAllNewsUseCase $useCase,
    )
    {
    }

    #[Route('/api/v1/news', name: 'api_news_all', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $response = iterator_to_array(($this->useCase)());
            return $this->json([
                'news' => $response,
            ]);
        } catch (\Throwable $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
