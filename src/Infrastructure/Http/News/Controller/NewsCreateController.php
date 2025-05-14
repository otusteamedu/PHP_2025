<?php

namespace App\Infrastructure\Http\News\Controller;

use App\Application\DTO\News\CreateNewsDTO;
use App\Application\UseCase\CreateNewsUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NewsCreateController extends AbstractController
{
    public function __construct(
        protected CreateNewsUseCase     $createNewsUseCase,
    ){}

    final public function create(Request $request): JsonResponse
    {
        try {
            $url = $request->request->get('url');
            $createdNewsDTO = new createNewsDTO($url);
            $createdNews = $this->createNewsUseCase->execute($createdNewsDTO);

            return $this->json(['STATUS' => 'OK', 'CREATED_NEWS' => $createdNews]);
        } catch(\Exception $exception) {
            return $this->json([
                'STATUS' => 'ERR',
                'MESSAGE' => $exception->getMessage(),
            ]);
        }
    }
}