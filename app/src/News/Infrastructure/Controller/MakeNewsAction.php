<?php
declare(strict_types=1);


namespace App\News\Infrastructure\Controller;

use App\News\Application\UseCase\MakeNews\MakeNewsRequest;
use App\News\Application\UseCase\MakeNews\MakeNewsUseCase;
use Psr\Log\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/news', name: 'app_api_news_make', methods: ['POST'])]
class MakeNewsAction extends AbstractController
{
    public function __construct(
        private readonly MakeNewsUseCase $makeNewsUseCase,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $link = $data['link'] ?? null;
        if($link === null) {
            throw new InvalidArgumentException('Link is not provided.');
        }
        $request = new MakeNewsRequest($data['link']);
        $id = ($this->makeNewsUseCase)($request)->news_id;

        return new JsonResponse(compact('id'));
    }

}